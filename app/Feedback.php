<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\User;

class Feedback extends Model
{
    protected $table = 'feedback';

    protected $fillable = [
        'userid', 'content'
    ];

    public function user()
    {
        return $this->belongsTo('App\User', 'userid');
    }
}
