<?php

namespace App;

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
        $userid = Auth::id();
        $admin = DB::select("SELECT admin FROM users WHERE id = '$userid'");

        if ($admin[0]->admin == 'Y') {
            return true;
        } else {
            return false;
        }
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

        if ($result !== null){
            return true;
        }
        return false;
    }
}