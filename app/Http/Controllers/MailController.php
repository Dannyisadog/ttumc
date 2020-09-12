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

        Mail::send('emails.test', $data, function ($message) use ($from, $to) {
            $message->from($from['email'], $from['name']);
            $message->to($to['email'], $to['name'])->subject($from['subject']);
        });

        if (Mail::failures()) {
            print_r(Mail::failures());
        }
    }
}
