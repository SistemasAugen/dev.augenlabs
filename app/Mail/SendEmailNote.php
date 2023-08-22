<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendEmailNote extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($inputs)
    {
        $this->inputs = $inputs;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $mail = $this->subject('Notas '.date('d-m-Y').'  | Augenlabs')
            ->markdown('emails.requestrx')
            ->with(['inputs'=> $this->inputs]);

            foreach ($this->inputs as $key => $value) {
                $mail->attach($value,['as' => $value, 'mime' => 'image/png']);
            }
        return $mail;   
    }
}
