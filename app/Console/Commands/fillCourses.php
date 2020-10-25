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
    
    // 0 0 * * 1 /usr/bin/php /var/www/html/ttumc/artisan fill:courses
    protected $description = 'fill courses every week on Monday 00:00';

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
        $date = date('Y-m-d H:i:s');
        $redis_status = Redis::get(Course::STATUS_KEY);

        file_put_contents('/home/dannychen/fillcourse.log', $date . "  status: " . $redis_status . "\n", FILE_APPEND);
        if (!$redis_status) {
            Course::removeCourseThisWeek();
            exit;
        }

        Course::createCourseThisWeek();
        exit;
    }
}
