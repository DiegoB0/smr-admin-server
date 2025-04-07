<?php

// phpcs:ignoreFile

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendNotification extends Mailable
{
    use Queueable;
    use SerializesModels;

    public $notificationData;


    public function __construct($notificationData)
    {
        $this->notificationData = $notificationData;
    }

    public function build()
    {

        return $this->view('emails.notification') // Create this view later
            ->with('data', $this->notificationData)
            ->from('dev@smrheavymaq.com', 'SMR Heavy Maq')
            ->subject('New Notification from SMR Heavy Maq');
    }

}
