<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    public $timestamps = false;
    protected $table = 'schedule';
    protected $fillable = ['title', 'orderby', 'starttime'];

    public function user()
    {
        return $this->belongsTo('App\User', 'orderby');
    }
}