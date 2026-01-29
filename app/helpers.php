<?php

if (!function_exists('getActiveProviders')) {
    /**
     * Get the list of active payment providers based on settings
     * 
     * @return array Array of provider configurations with 'code', 'name', and 'desc' keys
     */
    function getActiveProviders(): array
    {
        $providers = [
            ['code' => 'manual', 'name' => __('Manual'), 'desc' => __('Record charges manually')],
        ];
        
        // Check if Stripe is enabled using group structure
        $stripeSettings = getPaymentProviderSettings('stripe', false);
        $stripeEnabled = !empty($stripeSettings['enabled']) && $stripeSettings['enabled'] == '1';
        
        if ($stripeEnabled) {
            $providers[] = ['code' => 'stripe', 'name' => __('Stripe'), 'desc' => __('Checkout & cards')];
        }
        
        return $providers;
    }
}

if (!function_exists('getPaymentProviderSettings')) {
    /**
     * Get all settings for a payment provider from database, with fallback to config
     * 
     * @param string $provider The provider group name (e.g., 'stripe', 'paypal')
     * @param bool $withFallback Whether to fallback to config values if setting is empty
     * @return array Array of settings with keys as stored in database (e.g., 'key', 'secret', 'enabled')
     */
    function getPaymentProviderSettings(string $provider, bool $withFallback = true): array
    {
        // Get all settings for the provider group
        $settings = \App\Models\Setting::where('group', $provider)
            ->pluck('value', 'key')
            ->toArray();
        
        // Add fallback values from config if needed
        if ($withFallback) {
            $configKey = 'services.' . $provider;
            $configValues = config($configKey, []);
            
            foreach ($configValues as $key => $configValue) {
                // Only use config value if database setting is empty or doesn't exist
                if (empty($settings[$key] ?? null) && !empty($configValue)) {
                    $settings[$key] = $configValue;
                }
            }
        }
        
        return $settings;
    }
}

