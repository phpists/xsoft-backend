<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class StoreStaffMail extends Mailable
{
    use Queueable, SerializesModels;

    public $staff;
    public $password;

    public function __construct($staff, $password)
    {
        $this->staff = $staff;
        $this->password = $password;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'XSoft - Запрошення в компанію',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.store_staff',
            with: [
                'staff' => $this->staff,
                'password' => $this->password
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
