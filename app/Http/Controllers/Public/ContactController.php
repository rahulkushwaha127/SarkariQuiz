<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\ContactSubmission;
use App\Services\CaptchaService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email'],
            'subject' => ['required', 'string', 'max:255'],
            'message' => ['required', 'string', 'max:10000'],
        ];
        if (CaptchaService::isEnabled()) {
            $rules['g-recaptcha-response'] = ['required', 'string'];
        }
        $validated = $request->validate($rules);

        if (CaptchaService::isEnabled()) {
            if (! CaptchaService::verify($request->input('g-recaptcha-response'), $request->ip())) {
                return response()->json([
                    'message' => 'CAPTCHA verification failed. Please try again.',
                    'errors' => ['g-recaptcha-response' => ['CAPTCHA verification failed.']],
                ], 422);
            }
        }

        ContactSubmission::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'subject' => $validated['subject'],
            'message' => $validated['message'],
        ]);

        return response()->json(['success' => true, 'message' => 'Message sent successfully. We will get back to you soon.']);
    }
}
