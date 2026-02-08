<?php

namespace App\Services\Notifications;

use App\Models\FcmToken;
use App\Models\InAppNotification;
use App\Models\NotificationSendLog;
use App\Models\NotificationTemplate;
use App\Models\User;
use App\Mail\DynamicTemplateMail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class NotificationManager
{
    /**
     * Send a notification to one or more users using a template key.
     *
     * @param  string       $templateKey  e.g. "payment_success"
     * @param  User|array   $users        Single user or array of users
     * @param  array        $variables    e.g. ['user_name' => 'Rahul', 'amount' => '₹499']
     */
    public function send(string $templateKey, User|array $users, array $variables = []): void
    {
        $template = NotificationTemplate::query()
            ->active()
            ->byKey($templateKey)
            ->first();

        if (! $template) {
            Log::warning("NotificationManager: template '{$templateKey}' not found or inactive.");
            return;
        }

        $users = is_array($users) ? $users : [$users];

        foreach ($users as $user) {
            $vars = array_merge($this->defaultVariables($user), $variables);

            try {
                if ($template->hasChannel('email') && $user->email) {
                    $this->sendEmail($template, $user, $vars);
                }

                if ($template->hasChannel('fcm')) {
                    $this->sendFcm($template, $user, $vars);
                }

                if ($template->hasChannel('in_app')) {
                    $this->sendInApp($template, $user, $vars);
                }
            } catch (\Throwable $e) {
                Log::error("NotificationManager: failed sending '{$templateKey}' to user {$user->id}", [
                    'error' => $e->getMessage(),
                ]);
            }
        }

        // Log the send
        NotificationSendLog::create([
            'type'            => 'template:' . $templateKey,
            'unique_key'      => $templateKey . ':' . now()->timestamp,
            'payload'         => ['variables' => $variables, 'channels' => $template->channels],
            'recipient_count' => count($users),
            'sent_at'         => now(),
        ]);
    }

    /* ------------------------------------------------------------------ */
    /*  Channel: Email                                                     */
    /* ------------------------------------------------------------------ */

    protected function sendEmail(NotificationTemplate $template, User $user, array $vars): void
    {
        $subject = NotificationTemplate::render($template->email_subject, $vars);
        $body    = NotificationTemplate::render($template->email_body, $vars);

        if ($subject === '' && $body === '') {
            return;
        }

        Mail::to($user->email)->send(new DynamicTemplateMail($subject, $body));
    }

    /* ------------------------------------------------------------------ */
    /*  Channel: FCM Push                                                  */
    /* ------------------------------------------------------------------ */

    protected function sendFcm(NotificationTemplate $template, User $user, array $vars): void
    {
        $title = NotificationTemplate::render($template->fcm_title, $vars);
        $body  = NotificationTemplate::render($template->fcm_body, $vars);

        if ($title === '' && $body === '') {
            return;
        }

        $tokens = FcmToken::where('user_id', $user->id)
            ->whereNull('revoked_at')
            ->pluck('token')
            ->toArray();

        if (empty($tokens)) {
            return;
        }

        $sender = new FcmSender();
        $sender->sendToTokens($tokens, [
            'notification' => [
                'title' => $title,
                'body'  => $body,
            ],
        ]);
    }

    /* ------------------------------------------------------------------ */
    /*  Channel: In-App                                                    */
    /* ------------------------------------------------------------------ */

    protected function sendInApp(NotificationTemplate $template, User $user, array $vars): void
    {
        $title = NotificationTemplate::render($template->in_app_title, $vars);
        $body  = NotificationTemplate::render($template->in_app_body, $vars);
        $url   = NotificationTemplate::render($template->in_app_url, $vars);

        if ($title === '' && $body === '') {
            return;
        }

        InAppNotification::create([
            'user_id' => $user->id,
            'type'    => 'template:' . $template->key,
            'title'   => $title,
            'body'    => $body,
            'url'     => $url ?: null,
        ]);
    }

    /* ------------------------------------------------------------------ */
    /*  Default variables (available for every template)                   */
    /* ------------------------------------------------------------------ */

    protected function defaultVariables(User $user): array
    {
        return [
            'user_name'  => $user->name ?? 'User',
            'user_email' => $user->email ?? '',
            'app_name'   => config('app.name', 'QuizWhiz'),
            'date'       => now()->format('d M Y'),
            'time'       => now()->format('h:i A'),
        ];
    }
}
