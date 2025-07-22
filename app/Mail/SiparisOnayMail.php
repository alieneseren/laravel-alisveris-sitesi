<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Siparis;

class SiparisOnayMail extends Mailable
{
    use Queueable, SerializesModels;

    public $siparis;

    /**
     * Create a new message instance.
     */
    public function __construct(Siparis $siparis)
    {
        $this->siparis = $siparis;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject('Siparişiniz Onaylandı - #' . $this->siparis->id)
                    ->view('emails.siparis-onay')
                    ->with([
                        'siparis' => $this->siparis
                    ]);
    }
}
