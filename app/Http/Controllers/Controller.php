<?php

namespace App\Http\Controllers;

use App\Band as Band;
use App\Course as Course;
use App\Feedback as Feedback;
use App\Schedule as Schedule;
use App\User as User;
use Auth;
use DB;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request as Request;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function showIndex()
    {
        return view('welcome');
    }

    public function showUserManagement()
    {
        if (User::isadmin()) {
            $user = User::all();
            return view('usermanagement', ['user' => $user]);
        }
        return redirect()->route('index');
    }
    public function changeUserAdmin(Request $request)
    {
        if (User::isadmin()) {
            $userid = $request->input('userid');
            $isadmin = $request->input('isadmin');
            $update = User::where('id', $userid)->update(['admin' => $isadmin]);
            return redirect()->route('usermanagement');
        } else {
            return redirect()->route('schedule');
        }
    }

    public function showBand()
    {
        if (Auth::check()) {
            $userid = Auth::id();
            $bandlist = Band::where('belongto', $userid)->get();
            return view('band', ['bandlist' => $bandlist]);
        }
        return redirect()->route('index');
    }

    public function createBand(Request $request)
    {
        if (Auth::check()) {
            $userid = Auth::id();
            $bandname = $request->input('bandname');

            $findband = Band::where('name', $bandname)->first();
            $findname = User::where('name', $bandname)->first();
            $findcourse = Course::where('title', $bandname)->first();

            if ($findband === null && $findname === null && $findcourse === null) {
                $band = new Band;
                $band->belongto = $userid;
                $band->name = $bandname;
                $band->save();

                return redirect()->route('band');
            }
            return redirect()->back()->with('error-msg', '團名重複或與使用者名稱重複');
        }
    }
    public function deleteBand(Request $request)
    {
        if (Auth::check()) {
            $belongto = $request->input('belongid');
            $bandname = $request->input('bandname');

            $findbandinSch = Schedule::where('title', $bandname)->first();
            if ($findbandinSch != null) {
                return redirect()->back()->with('del-error-msg', '請取消預約，若無法取消請等到下週再移除');
            } else {
                $delete = Band::where('name', $bandname)->where('belongto', $belongto)->delete();
                $delete = Schedule::where('title', $bandname)->where('orderby', $belongto)->delete();

                return redirect()->route('band');
            }
        }
    }

    public function showFeedback()
    {
        return view('feedback');
    }
    public function createfeedback(Request $request)
    {
        $newfeedback = $request->input('feedback');
        $userid = Auth::id();
        $feedback = new Feedback;
        $feedback->content = $newfeedback;
        $feedback->userid = $userid;
        $feedback->save();

        return redirect()->back()->with('success-msg', '成功送出');
    }

    public function showBandManagement()
    {
        if (Auth::check()) {
            $allband = DB::select("SELECT a.name as bandname, b.name as username FROM band a, users b WHERE a.belongto = b.id");

            return view("bandmanagement", ['allband' => $allband]);
        }
        return view("schedule");
    }
}