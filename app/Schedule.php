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
}
