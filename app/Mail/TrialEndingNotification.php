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

class TrialEndingNotification extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(
        public Billing $billing,
        public Team $team,
        public Plan $plan,
        public int $daysRemaining
    ) {}

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: __('Trial Ending Soon - :days Days Remaining', ['days' => $this->daysRemaining]),
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.trial-ending',
            with: [
                'billing' => $this->billing,
                'team' => $this->team,
                'plan' => $this->plan,
                'daysRemaining' => $this->daysRemaining,
            ],
        );
    }
}

