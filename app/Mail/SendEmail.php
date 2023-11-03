<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendEmail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(private $data)
    {
        $this->data = $data;
    }

    /**
     * Build the data.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('email', ["data" => $this->data]);
    }
}
