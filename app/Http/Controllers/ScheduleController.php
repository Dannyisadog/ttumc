<?php

namespace App\Http\Controllers;

use App\Band as Band;
use App\Course as Course;
use App\Schedule as Schedule;
use App\User as User;
use Auth;
use DB;
use Illuminate\Http\Request;

class ScheduleController extends Controller
{
    public function showSchedule()
    {
        $schedule = DB::SELECT('SELECT a.name, b.title, b.starttime FROM users a, schedule b WHERE a.id = b.orderby AND b.valid="Y"');
        $user = User::all();

        if (Auth::check()) {
            $username = Auth::user()->name;
            $userid = Auth::id();

            $userordercount = DB::select("SELECT COUNT(*) as count FROM schedule WHERE title = '$username'");
            $bandordercount = DB::select("SELECT name, ordercount as count FROM `band` WHERE belongto = $userid");

            $usercanorder = true;
            $bandcanorder = true;

            if ($userordercount[0]->count >= 4) {
                $usercanorder = false;
            }

            $bandordercount_arr = array();

            foreach ($bandordercount as $item) {
                if ($item->count < 4) {
                    $bandcanorder = true;
                    break;
                } else {
                    $bandcanorder = false;
                }
            }
            foreach ($bandordercount as $item) {
                $bandordercount_arr[$item->name] = $item->count;
            }

            $thisweek = strtotime("this week");

            $usereachdayCount = DB::SELECT("SELECT title, DATE_FORMAT(starttime, '%Y-%m-%d') as starttime, COUNT(*) as count FROM schedule WHERE title = '$username' GROUP BY title, DATE_FORMAT(starttime, '%Y-%m-%d')");

            $usereachdayCount_arr = array(0 => '', 1 => '', 2 => '', 3 => '', 4 => '', 5 => '', 6 => '');
            foreach ($usereachdayCount as $item) {
                $time = strtotime($item->starttime);
                $day_num = ($time - $thisweek + 86400) / 86400;
                $usereachdayCount_arr[$day_num] = $item->count;
            }

            if (User::belongBandCount() > 0) {
                $userid = Auth::id();
                $bandlist = Band::where('belongto', $userid)->get();
                $bandeachdayCount_arr = array();
                $bandseachdaycanorder_arr = array(0 => '', 1 => '', 2 => '', 3 => '', 4 => '', 5 => '', 6 => '');
                foreach ($bandlist as $item) {
                    $bandeachdayCount = DB::SELECT("SELECT title, DATE_FORMAT(starttime, '%Y-%m-%d') as starttime, COUNT(*) as count FROM schedule WHERE title = '$item->name' GROUP BY title, DATE_FORMAT(starttime, '%Y-%m-%d')");

                    $tmp_arr = array(0 => '', 1 => '', 2 => '', 3 => '', 4 => '', 5 => '', 6 => '');

                    foreach ($bandeachdayCount as $item2) {
                        $time = strtotime($item2->starttime);
                        $day_num = ($time - $thisweek + 86400) / 86400;
                        $tmp_arr[$day_num] = $item2->count;
                        if ($item2->count < 2) {
                            $bandseachdaycanorder_arr[$day_num] = 1;
                            break;
                        } else {
                            $bandseachdaycanorder_arr[$day_num] = 0;
                        }
                    }

                    foreach ($bandeachdayCount as $item2) {
                        $time = strtotime($item2->starttime);
                        $day_num = ($time - $thisweek + 86400) / 86400;
                        $tmp_arr[$day_num] = $item2->count;
                    }

                    $bandeachdayCount_arr[$item->name] = $tmp_arr;
                }

                return view('schedule', ['schedule' => $schedule, 'user' => $user, 'usereachdayCount' => $usereachdayCount_arr, 'bandeachdayCount' => $bandeachdayCount_arr, 'bandseachdaycanorder' => $bandseachdaycanorder_arr, 'userordercount' => $userordercount[0], 'bandlist' => $bandlist, 'usercanorder' => $usercanorder, 'bandcanorder' => $bandcanorder, 'bandordercount' => $bandordercount_arr]);
            } else {
                return view('schedule', ['schedule' => $schedule, 'user' => $user, 'usereachdayCount' => $usereachdayCount_arr, 'usercanorder' => $usercanorder, 'bandcanorder' => $bandcanorder, 'bandordercount' => $bandordercount, 'userordercount' => $userordercount[0]]);
            }
        } else {
            return view('schedule', ['schedule' => $schedule, 'user' => $user]);
        }
    }
    public function createSchedule(Request $request)
    {
        if (!Auth::check()) {
            return redirect()->route('schedule');
        }
        $user = auth()->user()->id;
        $date = $request->input('date');
        $title = $request->input('title');

        $schedulefind = Schedule::where('starttime', $date)->first();
        if ($schedulefind === null) {
            $schedule = new Schedule;
            $schedule->orderby = $user;
            $schedule->title = $title;
            $schedule->starttime = $date;
            Band::where('name', $title)->where('belongto', $user)->update(["ordercount" => DB::raw('ordercount+1')]);
            $schedule->save();

            return redirect()->route('schedule');
        } else {
            return redirect()->route('schedule');
        }
    }
    public function deleteSchedule(Request $request)
    {
        if (!Auth::check()) {
            return redirect()->route('schedule');
        }
        $user = $request->input('userid');
        $time = $request->input('date');
        $title = $request->input('title');

        Band::where('name', $title)->where('belongto', $user)->update(["ordercount" => DB::raw('ordercount-1')]);
        $delete = Schedule::where('orderby', $user)->where('starttime', $time)->delete();

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
}
