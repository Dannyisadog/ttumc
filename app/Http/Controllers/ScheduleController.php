<?php

namespace App\Http\Controllers;

use App\Band as Band;
use App\Course as Course;
use App\Schedule as Schedule;
use App\User as User;
use Auth;
use DB;
use SweetAlert;
use Illuminate\Http\Request;

class ScheduleController extends Controller
{
    public function showSchedule()
    {
        if (Auth::check()) {
            $user = Auth::user();
        }
        $schedules = $this->getThisWeekSchedules();

        $schedule_map = [];

        foreach ($schedules as $schedule) {
            $key = $schedule->starttime;
            $user = User::find($schedule->orderby);
            $schedule_map[$key] = [
                "user_id" => $user->id,
                "user_name" => $user->name,
                "schedule_id" => $schedule->id
            ];
        }

        $schedule_data = [];

        if (isset($user)) {
            $schedule_data['user'] = $user;
        }
        $schedule_data['schedule_map'] = $schedule_map;

        return view(
            'schedule',
            $schedule_data
        );
    }
    public function createSchedule(Request $request)
    {
        if (!Auth::check()) {
            return redirect()->route('schedule');
        }
        $user = Auth::user();
        $date = $request->input('date');

        $schedule = Schedule::where('starttime', $date)->first();

        if ($schedule) {
            return redirect()->route('schedule');
        }

        Schedule::create([
            'title' => $user->name,
            'orderby' => $user->id,
            'starttime' => $date,
        ]);

        SweetAlert::success('預約成功');
        return redirect()->route('schedule');
    }
    public function deleteSchedule(Request $request)
    {
        if (!Auth::check()) {
            return redirect()->route('schedule');
        }

        $user = Auth::user();
        $schedule_id = $request->input("schedule_id");

        $schedule = Schedule::where('id', $schedule_id)->first();

        if (!$schedule) {
            SweetAlert::error('預約時段不存在', 'Error Message');
            return redirect()->route('schedule');
            exit;
        }

        if ($schedule->orderby != $user->id) {
            SweetAlert::error('無法刪除不屬於你的時段', 'Error Message');
            return redirect()->route('schedule');
            exit;
        }

        Schedule::destroy($schedule_id);

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

    private function getThisWeekSchedules()
    {
        $week_first_day = date('Y-m-d', strtotime('monday this week'));
        $week_last_day = date('Y-m-d', strtotime('monday this week') + 86400 * 7);
        return Schedule::where('valid', 'Y')
            ->where('starttime', '>=', $week_first_day)
            ->where('starttime', '<', $week_last_day)
            ->get();
    }
}