<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;

class PasswordResetMail extends Mailable
{
    public $resetToken;

    public function __construct($resetToken)
    {
        $this->resetToken = $resetToken;
    }

    public function build()
    {
        return $this->subject('Password Reset Token')
            ->view('emails.password_reset')
            ->with(['resetToken' => $this->resetToken]);
    }
}
