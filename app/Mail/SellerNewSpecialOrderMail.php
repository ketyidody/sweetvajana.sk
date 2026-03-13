<?php

namespace App\Mail;

use App\Models\SpecialOrder;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SellerNewSpecialOrderMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public SpecialOrder $specialOrder) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Nová špeciálna objednávka / New Special Order – '.$this->specialOrder->product_name);
    }

    public function content(): Content
    {
        return new Content(view: 'emails.seller_new_special_order');
    }
}
