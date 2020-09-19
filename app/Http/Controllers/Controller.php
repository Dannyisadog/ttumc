<?php

namespace App\Http\Controllers;

use App\Band as Band;
use App\BandUserMapping;
use App\Feedback as Feedback;
use App\Schedule as Schedule;
use App\User as User;
use Auth;
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

    public function showUserBands()
    {
        if (!Auth::check()) {
            return redirect()->route('index');
        }

        $user = Auth::user();
        $bandUserMappings = BandUserMapping::where('user_id', $user->id)->get();
        $bands = [];
        foreach ($bandUserMappings as $bandUserMapping) {
            $bands[] = $bandUserMapping->band;
        }

        return view('band', ['bands' => $bands]);
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
        $feedbacks = Feedback::all();

        return view('feedback', ['feedbacks' => $feedbacks]);
    }
    public function createfeedback(Request $request)
    {
        $feedback = $request->input('feedback');

        if (!Auth::check()) {
            return redirect()->back()->with('error-msg', '尚未登入');    
        }

        $user = Auth::user();

        Feedback::create([
            "userid" => $user->id,
            "content" => $feedback
        ]);

        return redirect()->route('feedback');
    }
}
