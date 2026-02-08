<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NotificationTemplate;
use App\Services\Notifications\NotificationManager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationTemplatesController extends Controller
{
    public function index()
    {
        $templates = NotificationTemplate::query()
            ->orderBy('is_active', 'desc')
            ->orderBy('name')
            ->get();

        return view('admin.notification-templates.index', compact('templates'));
    }

    public function edit(NotificationTemplate $notificationTemplate)
    {
        return view('admin.notification-templates.edit', [
            'template' => $notificationTemplate,
        ]);
    }

    public function update(Request $request, NotificationTemplate $notificationTemplate)
    {
        $data = $request->validate([
            'name'            => ['required', 'string', 'max:120'],
            'description'     => ['nullable', 'string', 'max:500'],
            'channels'        => ['nullable', 'array'],
            'channels.*'      => ['string', 'in:email,fcm,in_app'],
            'email_subject'   => ['nullable', 'string', 'max:250'],
            'email_body'      => ['nullable', 'string', 'max:65000'],
            'fcm_title'       => ['nullable', 'string', 'max:250'],
            'fcm_body'        => ['nullable', 'string', 'max:2000'],
            'in_app_title'    => ['nullable', 'string', 'max:250'],
            'in_app_body'     => ['nullable', 'string', 'max:2000'],
            'in_app_url'      => ['nullable', 'string', 'max:250'],
            'is_active'       => ['nullable'],
        ]);

        $data['is_active'] = (bool) ($data['is_active'] ?? false);
        $data['channels']  = $data['channels'] ?? [];

        $notificationTemplate->update($data);

        return redirect()
            ->route('admin.notification-templates.index')
            ->with('status', 'Template updated: ' . $notificationTemplate->name);
    }

    /**
     * Preview: render a template with sample data.
     */
    public function preview(Request $request, NotificationTemplate $notificationTemplate)
    {
        $vars = collect($notificationTemplate->available_variables ?? [])
            ->mapWithKeys(fn ($v) => [$v => $this->sampleValue($v)])
            ->toArray();

        $channel = $request->input('channel', 'email');

        $rendered = match ($channel) {
            'email' => [
                'subject' => NotificationTemplate::render($notificationTemplate->email_subject, $vars),
                'body'    => NotificationTemplate::render($notificationTemplate->email_body, $vars),
            ],
            'fcm' => [
                'title' => NotificationTemplate::render($notificationTemplate->fcm_title, $vars),
                'body'  => NotificationTemplate::render($notificationTemplate->fcm_body, $vars),
            ],
            'in_app' => [
                'title' => NotificationTemplate::render($notificationTemplate->in_app_title, $vars),
                'body'  => NotificationTemplate::render($notificationTemplate->in_app_body, $vars),
                'url'   => NotificationTemplate::render($notificationTemplate->in_app_url, $vars),
            ],
            default => [],
        };

        return response()->json([
            'ok'        => true,
            'channel'   => $channel,
            'variables' => $vars,
            'rendered'  => $rendered,
        ]);
    }

    /**
     * Send a test notification to the current admin user.
     */
    public function sendTest(NotificationTemplate $notificationTemplate)
    {
        $manager = new NotificationManager();
        $manager->send($notificationTemplate->key, Auth::user(), [
            'amount'             => '₹499',
            'plan_name'          => 'Pro Plan',
            'transaction_id'     => 'TEST_' . strtoupper(uniqid()),
            'expiry_date'        => now()->addDays(7)->format('d M Y'),
            'contest_name'       => 'Weekly Challenge',
            'minutes'            => '5',
            'announcement_title' => 'Test Announcement',
            'announcement_body'  => 'This is a test announcement from the admin panel.',
            'streak_days'        => '30',
            'batch_name'         => 'Test Batch',
            'app_url'            => config('app.url'),
        ]);

        return back()->with('status', 'Test notification sent to your account (' . Auth::user()->email . ').');
    }

    /* ------------------------------------------------------------------ */
    /*  Helpers                                                            */
    /* ------------------------------------------------------------------ */

    private function sampleValue(string $variable): string
    {
        return match ($variable) {
            'user_name'          => 'John Doe',
            'user_email'         => 'john@example.com',
            'app_name'           => config('app.name', 'QuizWhiz'),
            'app_url'            => config('app.url', 'http://localhost'),
            'date'               => now()->format('d M Y'),
            'time'               => now()->format('h:i A'),
            'amount'             => '₹499',
            'plan_name'          => 'Pro Plan',
            'transaction_id'     => 'pay_TXN123456',
            'expiry_date'        => now()->addDays(30)->format('d M Y'),
            'contest_name'       => 'Weekly Challenge',
            'minutes'            => '5',
            'announcement_title' => 'Sample Announcement',
            'announcement_body'  => 'This is a sample announcement body text.',
            'streak_days'        => '30',
            'batch_name'         => 'Batch Alpha',
            default              => '[' . $variable . ']',
        };
    }
}
