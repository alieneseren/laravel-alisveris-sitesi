<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class VerificationOtpMail extends Mailable
{
    use Queueable, SerializesModels;

    public $otp;
    public $kullaniciAdi;

    public function __construct($otp, $kullaniciAdi)
    {
        $this->otp = $otp;
        $this->kullaniciAdi = $kullaniciAdi;
    }

    public function build()
    {
        return $this->subject('E-posta Doğrulama Kodu')
                    ->view('emails.verification-otp');
    }
}