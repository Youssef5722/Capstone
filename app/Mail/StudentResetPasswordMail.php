<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class StudentResetPasswordMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly string $token,
        public readonly string $email,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: __('cms.auth.reset_password_title') . ' — CMS',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'mail.student-reset-password',
            with: [
                'resetUrl' => url('/student/reset-password/' . $this->token),
                'email'    => $this->email,
            ],
        );
    }
}
