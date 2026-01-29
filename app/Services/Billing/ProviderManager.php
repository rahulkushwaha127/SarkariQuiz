<?php

namespace App\Services\Billing;

use App\Contracts\BillingProvider;
use Illuminate\Support\Facades\App;

class ProviderManager
{
    public function resolve(string $code): BillingProvider
    {
        $providers = config('billing.providers', []);
        $class = $providers[$code] ?? null;
        abort_unless($class, 404, __('Unknown billing provider.'));
        $provider = App::make($class);
        abort_unless($provider instanceof BillingProvider, 500, __('Invalid billing provider.'));
        return $provider;
    }
}


