<?php
namespace App\Http\Controllers;

use App\Band as Band;
use App\BandUserMapping as BandUserMapping;
use App\Course as Course;
use App\Schedule as Schedule;
use App\User as User;
use Auth;
use DB;
use SweetAlert;
use Illuminate\Http\Request;
use UxWeb\SweetAlert\SweetAlert as SweetAlertSweetAlert;

class BandController extends Controller
{
    function createBand(Request $request)
    {
        if (!Auth::check()) {
            SweetAlert::error('請先登入再新增樂團');
            return redirect()->route('band');
        }
        $user = Auth::user();

        $bandname = $request->input('bandname');

        $band = Band::where('name', 'like', '%' . $bandname . '%')->first();

        if ($band) {
            SweetAlert::warning('團名重複或太相似');
            return redirect()->route('band');
        }

        $band = Band::create([
            'lead' => $user->id,
            'name' => $bandname
        ]);

        BandUserMapping::create([
            "band_id" => $band->id,
            "user_id" => $user->id
        ]);

        SweetAlert::success('新增樂團成功', '團名: ' . $bandname);
        return redirect()->route('band');
    }

    public function showBand()
    {
        if (!Auth::check()) {
            return view("schedule");
        }

        $user = Auth::user();
        $bands = Band::all();

        $joined_band_ids = [];

        $bandUserMappings = $user->bandUserMappings;

        foreach ($bandUserMappings as $bandUserMapping) {
            $joined_band_ids[] = $bandUserMapping->band_id;
        }

        $band_join_map = [];

        foreach ($bands as $band) {
            if (in_array($band->id, $joined_band_ids)) {
                $band_join_map[$band->id] = true;
            } else {
                $band_join_map[$band->id] = false;
            }
        }

        $bandlist_data = [
            'bands' => $bands,
            'band_join_map' => $band_join_map
        ];

        return view("bandlist", $bandlist_data);
    }

    public function joinBand(Request $request)
    {
        if (!Auth::check()) {
            SweetAlert::error("請先登入");
            return redirect()->route('bandlist');
        }

        $user = Auth::user();

        $user_id = $request->input('user_id');
        $band_id = $request->input('band_id');

        if ($user_id != $user->id) {
            SweetAlert::error("身份錯誤");
            return redirect()->route('bandlist');
        }

        $band = Band::find($band_id);

        if (!$band) {
            SweetAlert::error("樂團不存在");
            return redirect()->route('bandlist');
        }

        $bandUserMapping = BandUserMapping::where("band_id", $band_id)
            ->where("user_id", $user_id)
            ->first();

        if ($bandUserMapping) {
            SweetAlert::error("已經加入此樂團", $band->name);
            return redirect()->route('bandlist');
        }

        BandUserMapping::create([
            'band_id' => $band_id,
            'user_id' => $user_id
        ]);

        SweetAlert::success("加入成功", $band->name);
        return redirect()->route('bandlist');
    }
}
