<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Band extends Model
{
    public $timestamps = false;
    protected $table = 'band';
    protected $fillable = [
        'name', 'lead'
    ];

    public function userMappings()
    {
        return $this->hasMany('App\BandUserMapping', 'band_id', 'id');
    }

    public function leader()
    {
        return $this->hasOne('App\User', 'id', 'lead');
    }

    public function getDateOrderCount($date)
    {
        $date = date("Y-m-d", strtotime($date));

        $schedules = Schedule::where('starttime', 'like', '%' . $date . '%')
            ->where('band_id', $this->id)
            ->get();

        return count($schedules);
    }

    public function getWeekOrderCount()
    {
        $week_first_day = date('Y-m-d', strtotime('monday this week'));
        $week_last_day = date('Y-m-d', strtotime('monday this week') + 86400 * 7);

        $schedules = Schedule::where('starttime', '>=', $week_first_day)
            ->where('starttime', '<=', $week_last_day)
            ->where('band_id', $this->id)
            ->get();

        return count($schedules);
    }
}