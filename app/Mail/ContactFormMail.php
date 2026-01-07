<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue; // 1. Pastikan ini ada
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Address; // 2. Tambah ini

// 3. TAMBAHKAN 'implements ShouldQueue' DI SINI
class ContactFormMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Pesan Baru dari Website', // Set Subject di sini
            // 4. Tambahkan Reply-To agar saat di Gmail Anda klik 'Reply',
            // langsung membalas ke email pengirim (bukan email website sendiri)
            replyTo: [
                new Address($this->data['email'], $this->data['name']),
            ],
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.contact',
        );
    }

    public function attachments(): array
    {
        return [];
    }

    // 5. HAPUS function build() !!
    // Karena Anda sudah pakai envelope() dan content() di atas (Laravel 9+ style).
}
