<?php

namespace App\Http\Controllers;

use App\Services\Billing\ProviderManager;
use Illuminate\Http\Request;

class WebhookController extends Controller
{
    public function handle(Request $request, string $provider, ProviderManager $providers)
    {
        $providerImpl = $providers->resolve($provider);
        $providerImpl->handleWebhook($request);
        return response()->json(['ok' => true]);
    }
}


