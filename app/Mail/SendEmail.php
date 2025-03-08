<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $data;

    /**
     * Create a new message instance.
     *
     * @param array $data
     * @return void
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * Build the message.
     *
     * @return \Illuminate\Mail\Mailable
     */
    public function build()
    {
        return $this->view('test')
            ->with('data', $this->data)
            ->from('smr@smrheavymaq.com', 'SMR Heavy Maq')
            ->replyTo($this->data['sender_email'])
            ->subject('Nuevo mensaje de: ' . $this->data['sender_name']);
    }
}
