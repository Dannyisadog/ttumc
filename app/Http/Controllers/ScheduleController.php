<?php

namespace App\Http\Controllers;

use App\Band as Band;
use App\Course as Course;
use App\Schedule as Schedule;
use App\User as User;
use App\Week as Week;
use Auth;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use SweetAlert;
use Illuminate\Routing\Controller as BaseController;

class ScheduleController extends BaseController
{
    public function showSchedule(Request $request)
    {
        // $this->getSchedules();
        // exit;
        $selectors = [1, 2, 3, 4, 5, 6, 7];
        $selector_weekday_map = [
            1 => '星期一',
            2 => '星期二',
            3 => '星期三',
            4 => '星期四',
            5 => '星期五',
            6 => '星期六',
            7 => '星期日',
        ];

        if (Auth::check()) {
            $user = Auth::user();
            $schedule_data['user'] = $user;

            $week_can_order = $this->weekCanOrderByCount();
            $date_can_order_map = $this->dateCanOrderByCount();

            $schedule_data['week_can_order'] = $week_can_order;
            $schedule_data['date_can_order_map'] = $date_can_order_map;
        }

        $selector_mw = date('w') == 0 ? 7 : date('w');

        if ($request->input('selector') && in_array($request->input('selector'), $selectors)) {
            $selector_mw = $request->input('selector');
        }

        $schedule_data['selector'] = $selector_mw;
        $schedule_data['selector_weekday_map'] = $selector_weekday_map;

        $schedules = $this->getThisWeekSchedules();

        $schedule_map = [];

        foreach ($schedules as $schedule) {
            $key = $schedule->starttime;

            $orderby = $schedule->orderby;

            $schedule_belongs_to = [];
            $order_title = null;

            if ($orderby == 'user') {
                $user = User::find($schedule->user_id);
                $schedule_belongs_to[] = $user->id;
                $order_title = $user->name;
            } elseif ($orderby == 'admin') {
                $course = Course::find($schedule->course_id);
                $schedule_belongs_to[] = $course->id;
                $order_title = $course->title;
            } elseif ($orderby == 'band') {
                $band = Band::find($schedule->band_id);
                $order_title = $band->name;
                $bandUserMappings = $band->userMappings;

                foreach ($bandUserMappings as $bandUserMapping) {
                    $user = $bandUserMapping->user;
                    $schedule_belongs_to[] = $user->id;
                }
            }

            $schedule_map[$key] = [
                "user_ids" => $schedule_belongs_to,
                "order_title" => $order_title,
                "schedule_id" => $schedule->id,
            ];
        }

        $schedule_data['schedule_map'] = $schedule_map;

        return view('schedule', $schedule_data);
    }
    public function order_check(Request $request)
    {
        $datetime = $request->input('datetime');
        $result = [
            'status' => true,
            'can_multi_order' => true,
            'msg' => '預約成功',
        ];

        try {
            if (!Auth::check()) {
                throw new Exception("尚未登入");
            }
            $user = Auth::user();

            $bandUserMappings = $user->bandUserMappings;

            if (count($bandUserMappings) == 0 && $this->user_order($datetime)) {
                $result['can_multi_order'] = false;
            } else {
                $result['msg'] = '請選擇預約身份';
                $result['identities'] = $this->getOrderIdentities($datetime);
            }
        } catch (Exception $e) {
            $result['status'] = false;
            $result['msg'] = $e->getMessage();
        }

        echo json_encode($result);
    }
    public function order(Request $request)
    {
        $identity = $request->input('identity');
        $datetime = $request->input('datetime');

        $result = [
            'status' => true,
            'msg' => '預約成功',
        ];

        try {
            if (!Auth::check()) {
                throw new Exception("尚未登入");
            }
            $user = Auth::user();
            $order_type = $identity['order_type'];
            if (!$this->checkOrderType($order_type)) {
                throw new Exception('order_type error');
            }
            if ($order_type == 'user') {
                if ($identity['user_id'] != $user->id) {
                    throw new Exception('user_id error');
                }
                if ($user->getWeekOrderCount() >= 4 && $user->getDateOrderCount($datetime) >= 2) {
                    throw new Exception('order limit exceed');
                }
                if (!$this->checkDateCanOrder($datetime)) {
                    throw new Exception('the datetime had been order');
                }
                $this->user_order($datetime);
            } elseif ($order_type == 'band') {
                $band_id = $identity['band_id'];
                $this->band_order($datetime, $band_id);
            }
        } catch (Exception $e) {
            $result['status'] = false;
            $result['msg'] = $e->getMessage();
        }

        echo json_encode($result);
    }

    public function deleteSchedule(Request $request)
    {
        if (!Auth::check()) {
            return redirect()->route('schedule');
        }
        $user = Auth::user();
        try {
            $schedule_id = $request->input("schedule_id");

            $schedule = Schedule::where('id', $schedule_id)->first();

            if (!$schedule) {
                throw new Exception("預約時段不存在");
            }

            $user_ids = [];

            if ($schedule->orderby == 'user') {
                $user_ids[] = $schedule->user_id;
            } elseif ($schedule->orderby == 'band') {
                $userMappings = $schedule->band->userMappings;
                foreach ($userMappings as $userMapping) {
                    $user_ids[] = $userMapping->user_id;
                }
            }

            if (!in_array($user->id, $user_ids)) {
                throw new Exception("無法刪除不屬於你的時段");
            }

            Schedule::destroy($schedule_id);
        } catch (Exception $e) {
            SweetAlert::error($e);
        }

        SweetAlert::success('取消成功');
        return redirect()->route('schedule');
    }
    public function showScheduleMgm()
    {
        if (!Auth::check()) {
            return redirect()->route('schedule');
        }

        if (!User::isAdmin()) {
            return redirect()->route('schedule');
        }

        $course_status = Redis::get(Course::STATUS_KEY);

        $course_status_msg = "";

        if ($course_status) {
            $course_status_msg = "正常";
        } else {
            $course_status_msg = "暫停";
        }

        $courses = Course::all();

        $courses_date_key = [];

        foreach ($courses as $course) {
            $date_key = $course->day . "-" . date("H:i", strtotime($course->starttime));
            $courses_date_key[$date_key] = $course;
        }

        $data = [
            'courses' => $courses_date_key,
            'course_status' => $course_status_msg
        ];

        return view('schedulemgm', $data);
    }

    public function createCourse(Request $request)
    {
        if (!Auth::check()) {
            return redirect()->route('schedule');
        }
        if (!User::isAdmin()) {
            SweetAlert::error('權限不足');
            return redirect()->route('schedule');
        }
        $user = Auth::user();
        $title = $request->input('course');
        $time = $request->input('time');
        $day = explode("-", $time)[0];
        $starttime = explode("-", $time)[1] . ":00";

        $course = Course::where('day', $day)
            ->where('starttime', $starttime)
            ->first();

        if ($course) {
            SweetAlert::error("課程已存在");
            return redirect()->route('schedulemgm');
        }

        $course = Course::create([
            'title' => $title,
            'day' => $day,
            'starttime' => $starttime,
            'created_by' => $user->id,
        ]);

        $thisweek = strtotime("this week");
        $date = date('Y-m-d', $thisweek + 86400 * ($day - 1));
        $daytime = $date . " " . $starttime;

        $schedulefind = Schedule::where('starttime', $daytime)->first();
        if ($schedulefind !== null) {
            $delete = Schedule::where('starttime', $daytime)->delete();
        }

        Schedule::create([
            'title' => $title,
            'orderby' => 'admin',
            'course_id' => $course->id,
            'starttime' => $daytime,
        ]);

        return redirect()->route('schedulemgm');
    }

    public function deleteCourse(Request $request)
    {
        if (!Auth::check()) {
            return redirect()->route('schedule');
        }

        $time = $request->input('time');
        $starttime = explode('-', $time)[1];
        $day = explode('-', $time)[0];

        $delete = Course::where('starttime', $starttime)->where('day', $day)->delete();

        $thisweek = strtotime("this week");
        $date = date('Y-m-d', $thisweek + 86400 * ($day - 1));
        $daytime = $date . " " . $starttime;

        $delete = Schedule::where('starttime', $daytime)->delete();
        return redirect()->route('schedulemgm');
    }

    public function pauseCourse(Request $request)
    {
        if (!Auth::check()) {
            return redirect()->route('schedule');
        }

        Redis::set(Course::STATUS_KEY, 0);

        Course::removeCourseThisWeek();

        return redirect()->route('schedule');
    }
    public function resumeCourse(Request $request)
    {
        if (!Auth::check()) {
            return redirect()->route('schedule');
        }

        Redis::set(Course::STATUS_KEY, 1);

        Course::createCourseThisWeek();

        return redirect()->route('schedule');
    }

    public function getSchedules()
    {
        $user = null;
        
        if (Auth::check()) {
            $user = Auth::user();
        }

        $this_week_schedules = $this->getThisWeekSchedules();
        $new_this_week_schedules = [];

        foreach ($this_week_schedules as $schedule) {
            $new_this_week_schedules[strtotime($schedule->starttime)] = $schedule;
        }

        $this_week_schedules = $new_this_week_schedules;

        $start_hour = 8;
        $end_hour = 23;

        $schedules = [];

        $current_time = strtotime(date('Y-m-d H:i:s'));

        $this_week_dates = $this->getThisWeekDates();

        for ($hour = $start_hour; $hour <= $end_hour; $hour++) {
            foreach ($this_week_dates as $date) {
                $title = null;
                $orderby = null;
                $belongs_to = null;
                $can_order = true;
                $is_owner = false;

                $starttime = strtotime(date("{$date} {$hour}:00:00"));

                if (isset($new_this_week_schedules[$starttime])) {
                    $title = $new_this_week_schedules[$starttime]->title;
                    $orderby = $new_this_week_schedules[$starttime]->orderby;
                    $belongs_to = $new_this_week_schedules[$starttime]->getOrderById();
                    $can_order = false;
                }

                if ($starttime < $current_time) {
                    $can_order = false;
                }

                if ($user && isset($new_this_week_schedules[$starttime]) && $new_this_week_schedules[$starttime]->user()->id = $user->id) {
                    $is_owner = true;
                }

                $schedules[$hour][] = [
                    'title' => $title,
                    'orderby' => $orderby,
                    'belongs_to' => $belongs_to,
                    'can_order' => $can_order,
                    'is_owner' => $is_owner
                ];
            }
        }

        echo json_encode($schedules);
    }

    private function user_order($datetime)
    {
        $date_can_order = $this->checkDateCanOrder($datetime);

        if (!$date_can_order) {
            throw new Exception("此時段已被預約或是有其他課程", "請選擇其他時段");
        }

        $user = Auth::user();

        $order_data = [
            "title" => $user->name,
            'orderby' => 'user',
            'user_id' => $user->id,
            'starttime' => $datetime,
        ];

        Schedule::create($order_data);

        return true;
    }

    private function band_order($datetime, $band_id)
    {
        $date_can_order = $this->checkDateCanOrder($datetime);

        if (!$date_can_order) {
            throw new Exception("此時段已被預約或是有其他課程", "請選擇其他時段");
        }

        $user = Auth::user();

        $bandUserMappings = $user->bandUserMappings;

        $band_id_belongs_user = false;

        foreach ($bandUserMappings as $bandUserMapping) {
            if ($bandUserMapping->band_id == $band_id) {
                $band_id_belongs_user = true;
            }
        }

        if (!$band_id_belongs_user) {
            throw new Exception("band id doesn't belong user");
        }

        $band = Band::find($band_id);

        $order_data = [
            "title" => $band->name,
            'orderby' => 'band',
            'band_id' => $band->id,
            'starttime' => $datetime,
        ];

        Schedule::create($order_data);

        return true;
    }

    private function getThisWeekSchedules()
    {
        list($start, $end) = Week::getWeekRange();

        return Schedule::where('valid', 'Y')
                        ->whereBetween('starttime', [$start, $end])
                        ->get();
           
    }

    private function getThisWeekDates()
    {
        $dates = [];

        $week_first_day = date('Y-m-d', strtotime('monday this week'));

        $dates[] = $week_first_day;

        for ($i = 1; $i <= 6; $i++) {
            $dates[] = date('Y-m-d', strtotime("$week_first_day +" . $i . " day"));
        }

        return $dates;
    }

    private function checkOrderType($type)
    {
        $result = null;

        if (in_array($type, Schedule::$order_types)) {
            $result = $type;
        }

        return $result;
    }

    private function checkDateCanOrder($date)
    {
        $result = true;

        $schedule = Schedule::where('starttime', $date)->first();

        if ($schedule) {
            $result = false;
        }

        return $result;
    }

    private function dateCanOrderByCount()
    {
        $thisWeekDates = $this->getThisWeekDates();

        $user = Auth::user();

        $bands = $user->getBands();

        $dateCanOrderMap = [];

        foreach ($thisWeekDates as $date) {
            $dateCanOrderMap[$date] = false;
        }

        foreach ($thisWeekDates as $date) {
            if ($user->getDateOrderCount($date) < 2) {
                $dateCanOrderMap[$date] = true;
                continue;
            }
            foreach ($bands as $band) {
                if ($band->getDateOrderCount($date) < 2) {
                    $dateCanOrderMap[$date] = true;
                }
            }
        }

        return $dateCanOrderMap;
    }

    private function weekCanOrderByCount()
    {
        $can_order = false;

        $user = Auth::user();

        $bands = $user->getBands();

        if ($user->getWeekOrderCount() < 4) {
            $can_order = true;
        }

        foreach ($bands as $band) {
            if ($band->getWeekOrderCount() < 4) {
                $can_order = true;
            }
        }

        return $can_order;
    }

    private function getOrderIdentities($datetime)
    {
        $identities = [];

        $user = Auth::user();

        $bands = $user->getBands();

        if ($user->getWeekOrderCount() < 4 && $user->getDateOrderCount($datetime) < 2) {
            $identities[] = [
                'order_type' => 'user',
                'user_id' => $user->id,
                'order_title' => $user->name,
            ];
        }

        foreach ($bands as $band) {
            if ($band->getWeekOrderCount() < 4 && $band->getDateOrderCount($datetime) < 2) {
                $identities[] = [
                    'order_type' => 'band',
                    'band_id' => $band->id,
                    'order_title' => $band->name,
                ];
            }
        }

        return $identities;
    }
}
