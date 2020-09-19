<?php

namespace App\Console\Commands;

use App\Course;
use App\Schedule;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Redis;

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
        $redis_status = Redis::get(Course::STATUS_KEY);

        if (!$redis_status) {
            Course::removeCourseThisWeek();
            exit;
        }

        Course::createCourseThisWeek();
    }
}
