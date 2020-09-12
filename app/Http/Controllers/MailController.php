<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Mail;

class MailController extends Controller
{
    public function sendTestMail()
    {
        exit;
        Mail::send('emails.test', ['key' => 'value'], function ($message) {
            $message->to('dannyisadog10@gmail.com', 'Danny')->subject('test email!');
        });

        if (Mail::failures()) {
            print_r(Mail::failures());
        } else {
            echo "成功";
        }
    }
}
