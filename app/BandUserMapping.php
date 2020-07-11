<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BandUserMapping extends Model
{
    public $timestamps = false;
    protected $table = 'band_users_mapping';
    protected $fillable = [
        'band_id', 'user_id'
    ];

    public function user()
    {
        return $this->hasOne('App\User', 'id', 'user_id');
    }

    public function band()
    {
        return $this->hasOne('App\Band', 'id', 'band_id');
    }
}