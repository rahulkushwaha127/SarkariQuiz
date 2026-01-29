<?php

return [
    'providers' => [
        'manual' => \App\Services\Billing\Providers\ManualProvider::class,
        'stripe' => \App\Services\Billing\Providers\StripeProvider::class,
    ],
];


