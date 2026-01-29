<?php

namespace App\Mail;

use App\Models\Billing;
use App\Models\Team;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ManualRequestApprovedNotification extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(
        public Billing $billing,
        public Team $team
    ) {}

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: __('Payment Request Approved - Invoice :invoice', ['invoice' => $this->billing->invoice_number ?? '']),
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.manual-request-approved',
            with: [
                'billing' => $this->billing,
                'team' => $this->team,
            ],
        );
    }
}

