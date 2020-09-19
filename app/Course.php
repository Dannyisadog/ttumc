<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Schedule;
use App\Week;

class Course extends Model
{
    public $timestamps = false;
    protected $table = "course";

    const STATUS_KEY = "schedule:course:status";

    protected $fillable = [
        'title', 'day', 'starttime', 'created_by',
    ];

    public function user()
    {
        return $this->belongsTo('App\User', 'created_by');
    }

    public static function removeCourseThisWeek()
    {
        list($start, $end) = Week::getWeekRange();

        $schedule_course = Schedule::whereBetween("starttime", [$start, $end])
                                   ->whereNotNull("course_id")
                                   ->delete();
    }

    public static function createCourseThisWeek()
    {
        self::removeCourseThisWeek();

        list($start, $end) = Week::getWeekRange();

        $courses = Course::all();

        $week_first_day = date('Y-m-d', strtotime('monday this week'));
        foreach ($courses as $course) {
            Schedule::create([
                'title' => $course->title,
                'orderby' => 'admin',
                'course_id' => $course->id,
                'starttime' => date('Y-m-d', strtotime($week_first_day) + 86400 * ($course->day - 1)) . " " . $course->starttime,
            ]);
        }
    }
}
