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
    public function __construct($inputs,$pdf, $title = '')
    {
        $this->inputs = $inputs;
        $this->pdf = $pdf;
        $this->title = $title;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $filename = $this->inputs['pvd'] . ' - ' . date('d-m-Y') . '.pdf';  // Ensure the filename has a .pdf extension

        return $this->subject($this->title . ' ' . $this->inputs['pvd'] . ' - ' . date('d-m-Y') . ' RX ' . $this->inputs['rx_rx'])
            ->markdown('emails.requestrx')
            ->with(['inputs' => $this->inputs])
            ->attachData($this->pdf, $filename, [
                'mime' => 'application/pdf'
            ]);
    }

}
