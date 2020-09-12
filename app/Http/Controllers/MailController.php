<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Mail;

class MailController extends Controller
{
    public function sendTestMail()
    {
        $from = [
            'email' => 'ttumc@ttumc.dannyisadog.com',
            'name' => 'ttumc',
            'subject' => 'test mail'
        ];

        $to = [
            'email' => 'dannyisadog10@gmail.com',
            'name' => 'Danny'
        ];

        $data = [
            'subject' => "test mail"
        ];

        $mail = new Mail;

        $mail::send('emails.test', ['key' => 'value'], function ($message) {
            $message->to('dannyisadog10@gmail.com', 'Danny')->subject('test email!');
        });

        if ($mail::failures()) {
            print_r($mail::failures());
        } else {
            echo "成功";
        }
    }
}
