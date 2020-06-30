<?php

namespace App\Console\Commands;

use DB;
use Illuminate\Console\Command;

class ScheduleRefresh extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'schedule:refresh';

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
        $sql = "INSERT INTO schedule_history (SELECT * FROM schedule)";
        DB::insert("$sql");
        $sql = "DELETE FROM schedule";
        DB::delete("$sql");

        $thisweek = strtotime("this week");
        $day = date('Y-m-d', $thisweek);
        $oneday = 86400;

        $sql = "SELECT title, starttime, day FROM schedule_course where valid = 'Y'";
        $course = DB::select("$sql");

        foreach ($course as $item) {
            $title = $item->title;
            $day = $item->day;
            $starttime = date('Y-m-d', $thisweek + $oneday * ($day - 1)) . " " . $item->starttime;
            $sql = "INSERT INTO schedule (orderby, title, starttime) VALUES (1,'$title', '$starttime')";
            $insert = DB::insert("$sql");
        }

        $sql = "UPDATE band SET ordercount = 0";
        DB::update("$sql");
    }
}