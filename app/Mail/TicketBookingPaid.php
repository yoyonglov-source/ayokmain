<?php

namespace App\Mail;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TicketBookingPaid extends Mailable
{
    use Queueable, SerializesModels;

    // Definisikan variabel public agar otomatis terbaca di file view blade email
    public $booking;

    public function __construct(Booking $booking)
    {
        $this->booking = $booking;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            // Subject email yang akan dilihat pertama kali oleh user
            subject: 'E-Ticket Resmi AyokMain - ' . $this->booking->venue->name,
        );
    }

    public function content(): Content
    {
        return new Content(
            // Arahkan ke file blade template email yang akan kita buat di Langkah 3
            view: 'emails.ticket_paid',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}