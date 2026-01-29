<?php

namespace App\Mail;

use App\Models\Billing;
use App\Models\Plan;
use App\Models\Team;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TrialStartedNotification extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(
        public Billing $billing,
        public Team $team,
        public Plan $plan
    ) {}

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: __('Trial Started - :plan', ['plan' => $this->plan->name]),
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.trial-started',
            with: [
                'billing' => $this->billing,
                'team' => $this->team,
                'plan' => $this->plan,
            ],
        );
    }
}

