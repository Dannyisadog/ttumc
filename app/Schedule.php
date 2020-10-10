<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    public $timestamps = false;
    protected $table = 'schedule';
    protected $fillable = ['title', 'user_id', 'band_id', 'course_id', 'orderby', 'starttime'];

    public static $order_types = ['user', 'band', 'admin'];

    public function user()
    {
        return $this->belongsTo('App\User', 'user_id', 'id');
    }

    public function band()
    {
        return $this->belongsTo('App\Band', 'band_id', 'id');
    }

    public function course()
    {
        return $this->belongsTo('App\Course', 'course_id', 'id');
    }

    public function getOrderById()
    {
        $orderby_id = null;

        switch ($this->order_by) {
            case 'user' :
                $orderby_id = $schedule->user_id;
                break;
            case 'band' :
                $orderby_id = $schedule->band_id;
                break;
            case 'course' :
                $orderby_id = $schedule->course_id;
                break;
            default :
                break;
        }

        return $orderby_id;
    }
}
