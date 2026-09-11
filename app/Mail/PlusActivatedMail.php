<?php

namespace App\Mail;

use App\Models\User;
use Carbon\CarbonInterface;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PlusActivatedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public User $user,
        public CarbonInterface $plusUntil,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Gefeliciteerd, Candidakuur Plus is actief',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'mail.plus-activated',
        );
    }
}
