<?php

namespace App\Http\Controllers;

use App\Band as Band;
use App\Course as Course;
use App\Schedule as Schedule;
use App\User as User;
use Auth;
use Exception;
use DB;
use SweetAlert;
use Illuminate\Http\Request;

class ScheduleController extends Controller
{
    public function showSchedule(Request $request)
    {
        $selectors = [1, 2, 3, 4, 5, 6, 7];
        $selector_weekday_map = [
            1 => '星期一',
            2 => '星期二',
            3 => '星期三',
            4 => '星期四',
            5 => '星期五',
            6 => '星期六',
            7 => '星期日'
        ];

        if (Auth::check()) {
            $user = Auth::user();
            $schedule_data['user'] = $user;

            $week_can_order = $this->weekCanOrderByCount();
            $date_can_order_map = $this->dateCanOrderByCount();

            $schedule_data['week_can_order'] = $week_can_order;
            $schedule_data['date_can_order_map'] = $date_can_order_map;
        }

        $selector_mw = 1;

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
                "schedule_id" => $schedule->id
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
            'msg' => '預約成功'
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
            'msg' => '預約成功'
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
        $course = DB::SELECT('SELECT a.name, b.title, DATE_FORMAT(b.starttime, "%H:%i") as starttime , b.day FROM users a, schedule_course b WHERE a.id = b.orderby AND b.valid="Y"');
        $user = User::all();
        return view('schedulemgm', ['course' => $course, 'user' => $user]);
    }

    public function createCourse(Request $request)
    {
        if (!Auth::check()) {
            return redirect()->route('schedule');
        }
        $user = Auth::id();
        $title = $request->input('course');
        $time = $request->input('time');
        $starttime = explode("-", $time)[1];
        $day = explode("-", $time)[0];

        $course = new Course;
        $course->orderby = $user;
        $course->title = $title;
        $course->starttime = $starttime;
        $course->day = $day;

        $course->save();

        $thisweek = strtotime("this week");
        $date = date('Y-m-d', $thisweek + 86400 * ($day - 1));
        $daytime = $date . " " . $starttime;

        $schedule = new Schedule;

        $schedulefind = Schedule::where('starttime', $daytime)->first();
        if ($schedulefind !== null) {
            $delete = Schedule::where('starttime', $daytime)->delete();
        }
        $schedule->orderby = $user;
        $schedule->title = $title;

        $schedule->starttime = $daytime;

        $schedule->save();

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
        $course = Course::all();

        foreach ($course as $item) {
            $day = $item->day;
            $starttime = $item->starttime;

            $thisweek = strtotime("this week");
            $date = date('Y-m-d', $thisweek + 86400 * ($day - 1));
            $daytime = $date . " " . $starttime;

            $delete = Schedule::where('starttime', $daytime)->delete();
        }

        return redirect()->route('schedule');
    }
    public function resumeCourse(Request $request)
    {
        if (!Auth::check()) {
            return redirect()->route('schedule');
        }

        $course = Course::all();

        foreach ($course as $item) {
            $day = $item->day;
            $starttime = $item->starttime;

            $thisweek = strtotime("this week");
            $date = date('Y-m-d', $thisweek + 86400 * ($day - 1));
            $daytime = $date . " " . $starttime;

            $schedule = new Schedule;

            $schedule->orderby = Auth::id();
            $schedule->title = $item->title;
            $schedule->starttime = $daytime;

            $schedule->save();
        }

        return redirect()->route('schedule');
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
            'starttime' => $datetime
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
            'starttime' => $datetime
        ];

        Schedule::create($order_data);

        return true;
    }

    private function getThisWeekSchedules()
    {
        $week_first_day = date('Y-m-d', strtotime('monday this week'));
        $week_last_day = date('Y-m-d', strtotime('monday this week') + 86400 * 7);
        return Schedule::where('valid', 'Y')
            ->where('starttime', '>=', $week_first_day)
            ->where('starttime', '<', $week_last_day)
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
                'order_title' => $user->name
            ];
        }

        foreach ($bands as $band) {
            if ($band->getWeekOrderCount() < 4 && $band->getDateOrderCount($datetime) < 2) {
                $identities[] = [
                    'order_type' => 'band',
                    'band_id' => $band->id,
                    'order_title' => $band->name
                ];
            }
        }

        return $identities;
    }
}
