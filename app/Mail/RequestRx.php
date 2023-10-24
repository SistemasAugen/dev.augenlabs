<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class RequestRx extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($inputs,$pdf)
    {
        $this->inputs = $inputs;
        $this->pdf = $pdf;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject($this->inputs['pvd'] . ' - ' . date('d-m-Y') . ' RX ' . $this->inputs['rx_rx'])
            ->markdown('emails.requestrx')
            ->with(['inputs'=> $this->inputs])
            ->attachData($this->pdf, $this->inputs['pvd'].' - '.date('d-m-Y'),[
                'mime' => 'application/pdf'
            ]);
    }
}
