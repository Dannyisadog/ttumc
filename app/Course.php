<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    public $timestamps = false;
    protected $table = "course";

    protected $fillable = [
        'title', 'day', 'starttime', 'created_by',
    ];

    public function user()
    {
        return $this->belongsTo('App\User', 'created_by');
    }
}
