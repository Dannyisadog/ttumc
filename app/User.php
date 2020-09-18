<?php

namespace App;

use App\Notifications\ResetPasswordNotification;
use App\Schedule;
use Auth;
use DB;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'lastlogintime',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public static function isAdmin()
    {
        if (!Auth::check()) {
            return false;
        }

        $user = Auth::user();

        if ($user->admin == 'Y') {
            return true;
        }
        return false;
    }
    public static function belongBandCount()
    {
        $userid = Auth::id();

        $result = DB::select("SELECT COUNT(*) as count FROM band WHERE belongto = '$userid'");

        return $result[0]->count;
    }
    public static function belongBand($name)
    {
        $userid = Auth::id();
        $result = Band::where("belongto", $userid)->where('name', $name)->first();

        if ($result !== null) {
            return true;
        }
        return false;
    }

    public function schedules()
    {
        return $this->hasMany('App\Schedule');
    }

    public function bandUserMappings()
    {
        return $this->hasMany('App\BandUserMapping', 'user_id', 'id');
    }

    public function getDateOrderCount($date)
    {
        $date = date("Y-m-d", strtotime($date));

        $schedules = Schedule::where('starttime', 'like', '%' . $date . '%')
            ->where('user_id', $this->id)
            ->get();

        return count($schedules);
    }

    public function getWeekOrderCount()
    {
        $week_first_day = date('Y-m-d', strtotime('monday this week'));
        $week_last_day = date('Y-m-d', strtotime('monday this week') + 86400 * 7);

        $schedules = Schedule::where('starttime', '>=', $week_first_day)
            ->where('starttime', '<=', $week_last_day)
            ->where('user_id', $this->id)
            ->get();

        return count($schedules);
    }

    public function getBands()
    {
        $bands = [];

        $bandUserMappings = [];

        if ($this->bandUserMappings) {
            $bandUserMappings = $this->bandUserMappings;
        }

        foreach ($bandUserMappings as $bandUserMapping) {
            $bands[] = $bandUserMapping->band;
        }

        return $bands;
    }

    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPasswordNotification($token));
    }
}
