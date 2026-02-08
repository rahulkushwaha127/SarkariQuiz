<?php

namespace Database\Seeders;

use App\Models\NotificationTemplate;
use Illuminate\Database\Seeder;

class NotificationTemplateSeeder extends Seeder
{
    public function run(): void
    {
        $templates = [
            [
                'key'         => 'welcome',
                'name'        => 'Welcome Email',
                'description' => 'Sent to a new user right after registration.',
                'channels'    => ['email', 'in_app'],
                'email_subject' => 'Welcome to {{app_name}}!',
                'email_body'    => '<h2>Hi {{user_name}},</h2>
<p>Welcome to <strong>{{app_name}}</strong>! We\'re excited to have you on board.</p>
<p>Start exploring quizzes, challenges, and compete with other students.</p>
<p><a class="btn" href="{{app_url}}">Open {{app_name}}</a></p>
<p>Happy learning!</p>',
                'fcm_title'     => null,
                'fcm_body'      => null,
                'in_app_title'  => 'Welcome to {{app_name}}!',
                'in_app_body'   => 'Hi {{user_name}}, your account is ready. Start exploring now!',
                'in_app_url'    => '/',
                'available_variables' => ['user_name', 'user_email', 'app_name', 'app_url', 'date'],
            ],
            [
                'key'         => 'payment_success',
                'name'        => 'Payment Confirmed',
                'description' => 'Sent after a payment is successfully verified.',
                'channels'    => ['email', 'fcm', 'in_app'],
                'email_subject' => 'Payment confirmed — ₹{{amount}}',
                'email_body'    => '<h2>Payment received!</h2>
<p>Hi {{user_name}},</p>
<div class="highlight-box">
    <div class="amount">₹{{amount}}</div>
    <p style="margin:4px 0 0;color:#64748b;font-size:13px;">{{plan_name}} · {{date}}</p>
</div>
<p>Your plan <strong>{{plan_name}}</strong> has been activated. Enjoy all the premium features!</p>
<p>Transaction ID: <code>{{transaction_id}}</code></p>
<p><a class="btn" href="{{app_url}}">Go to Dashboard</a></p>',
                'fcm_title'     => 'Payment confirmed!',
                'fcm_body'      => '₹{{amount}} paid for {{plan_name}}. Your plan is now active.',
                'in_app_title'  => 'Payment confirmed',
                'in_app_body'   => '₹{{amount}} paid for {{plan_name}}. Your plan is now active.',
                'in_app_url'    => '/plans',
                'available_variables' => ['user_name', 'amount', 'plan_name', 'transaction_id', 'app_name', 'app_url', 'date'],
            ],
            [
                'key'         => 'payment_failed',
                'name'        => 'Payment Failed',
                'description' => 'Sent when a payment attempt fails.',
                'channels'    => ['email', 'in_app'],
                'email_subject' => 'Payment failed — ₹{{amount}}',
                'email_body'    => '<h2>Payment could not be processed</h2>
<p>Hi {{user_name}},</p>
<p>We were unable to process your payment of <strong>₹{{amount}}</strong> for <strong>{{plan_name}}</strong>.</p>
<p>This could be due to insufficient funds, a bank decline, or a network issue. No amount has been charged.</p>
<p><a class="btn" href="{{app_url}}/plans">Try Again</a></p>
<p>If the issue persists, please contact support.</p>',
                'fcm_title'     => null,
                'fcm_body'      => null,
                'in_app_title'  => 'Payment failed',
                'in_app_body'   => 'Your payment of ₹{{amount}} for {{plan_name}} could not be processed. Please try again.',
                'in_app_url'    => '/plans',
                'available_variables' => ['user_name', 'amount', 'plan_name', 'app_name', 'app_url', 'date'],
            ],
            [
                'key'         => 'plan_activated',
                'name'        => 'Plan Activated',
                'description' => 'Sent when a user\'s plan is activated (free or paid).',
                'channels'    => ['fcm', 'in_app'],
                'email_subject' => null,
                'email_body'    => null,
                'fcm_title'     => 'Plan activated!',
                'fcm_body'      => 'Your {{plan_name}} plan is now active. Enjoy the features!',
                'in_app_title'  => 'Plan activated',
                'in_app_body'   => 'Your {{plan_name}} plan is now active.',
                'in_app_url'    => '/plans',
                'available_variables' => ['user_name', 'plan_name', 'app_name', 'date'],
            ],
            [
                'key'         => 'plan_expiring',
                'name'        => 'Plan Expiring Soon',
                'description' => 'Sent a few days before the user\'s plan expires.',
                'channels'    => ['email', 'fcm', 'in_app'],
                'email_subject' => 'Your {{plan_name}} plan expires on {{expiry_date}}',
                'email_body'    => '<h2>Your plan is expiring soon</h2>
<p>Hi {{user_name}},</p>
<p>Your <strong>{{plan_name}}</strong> plan will expire on <strong>{{expiry_date}}</strong>.</p>
<p>Renew now to keep enjoying all the premium features without interruption.</p>
<p><a class="btn" href="{{app_url}}/plans">Renew Plan</a></p>',
                'fcm_title'     => 'Plan expiring soon',
                'fcm_body'      => 'Your {{plan_name}} plan expires on {{expiry_date}}. Renew now!',
                'in_app_title'  => 'Plan expiring soon',
                'in_app_body'   => 'Your {{plan_name}} plan expires on {{expiry_date}}. Renew to continue.',
                'in_app_url'    => '/plans',
                'available_variables' => ['user_name', 'plan_name', 'expiry_date', 'app_name', 'app_url', 'date'],
            ],
            [
                'key'         => 'daily_quiz_reminder',
                'name'        => 'Daily Quiz Reminder',
                'description' => 'Sent daily to remind users about the daily challenge.',
                'channels'    => ['fcm', 'in_app'],
                'email_subject' => null,
                'email_body'    => null,
                'fcm_title'     => 'Daily challenge is live!',
                'fcm_body'      => 'Today\'s daily challenge is ready. Play now to keep your streak going!',
                'in_app_title'  => 'Daily challenge is live!',
                'in_app_body'   => 'Today\'s daily challenge is ready. Keep your streak going!',
                'in_app_url'    => '/daily',
                'available_variables' => ['user_name', 'app_name', 'date'],
            ],
            [
                'key'         => 'contest_starting',
                'name'        => 'Contest Starting Soon',
                'description' => 'Sent a few minutes before a contest begins.',
                'channels'    => ['fcm', 'in_app'],
                'email_subject' => null,
                'email_body'    => null,
                'fcm_title'     => '{{contest_name}} starts in {{minutes}} min!',
                'fcm_body'      => 'Get ready — {{contest_name}} is about to begin.',
                'in_app_title'  => 'Contest starting soon',
                'in_app_body'   => '{{contest_name}} starts in {{minutes}} minutes. Get ready!',
                'in_app_url'    => '/contests',
                'available_variables' => ['user_name', 'contest_name', 'minutes', 'app_name', 'date'],
            ],
            [
                'key'         => 'new_announcement',
                'name'        => 'New Announcement',
                'description' => 'Sent when admin or creator publishes an announcement.',
                'channels'    => ['email', 'fcm', 'in_app'],
                'email_subject' => '{{announcement_title}}',
                'email_body'    => '<h2>{{announcement_title}}</h2>
<p>Hi {{user_name}},</p>
<div class="highlight-box">{{{announcement_body}}}</div>
<p><a class="btn" href="{{app_url}}">Open {{app_name}}</a></p>',
                'fcm_title'     => '{{announcement_title}}',
                'fcm_body'      => '{{announcement_body}}',
                'in_app_title'  => '{{announcement_title}}',
                'in_app_body'   => '{{announcement_body}}',
                'in_app_url'    => null,
                'available_variables' => ['user_name', 'announcement_title', 'announcement_body', 'app_name', 'app_url', 'date'],
            ],
            [
                'key'         => 'streak_milestone',
                'name'        => 'Streak Milestone',
                'description' => 'Sent when a student reaches a streak milestone (7, 30, 100 days).',
                'channels'    => ['fcm', 'in_app'],
                'email_subject' => null,
                'email_body'    => null,
                'fcm_title'     => '{{streak_days}}-day streak!',
                'fcm_body'      => 'Congratulations {{user_name}}! You\'ve maintained a {{streak_days}}-day streak. Keep it up!',
                'in_app_title'  => '{{streak_days}}-day streak!',
                'in_app_body'   => 'Congratulations! You\'ve maintained a {{streak_days}}-day streak. Keep going!',
                'in_app_url'    => '/',
                'available_variables' => ['user_name', 'streak_days', 'app_name', 'date'],
            ],
            [
                'key'         => 'batch_joined',
                'name'        => 'Batch Joined',
                'description' => 'Sent when a student successfully joins a batch.',
                'channels'    => ['in_app'],
                'email_subject' => null,
                'email_body'    => null,
                'fcm_title'     => null,
                'fcm_body'      => null,
                'in_app_title'  => 'Joined batch: {{batch_name}}',
                'in_app_body'   => 'You\'ve successfully joined the batch "{{batch_name}}".',
                'in_app_url'    => '/my-batches',
                'available_variables' => ['user_name', 'batch_name', 'app_name', 'date'],
            ],
        ];

        foreach ($templates as $tpl) {
            NotificationTemplate::updateOrCreate(
                ['key' => $tpl['key']],
                array_merge($tpl, ['is_system' => true]),
            );
        }
    }
}
