<?php

namespace App\Console\Commands;

use App\Course;
use App\Schedule;
use Illuminate\Console\Command;

class fillCourses extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fill:courses';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
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
