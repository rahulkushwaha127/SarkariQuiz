<x-mail::message>
# {{ __('Welcome!') }}

{{ __('Hello :name,', ['name' => $user->name]) }}

{{ __('Thank you for joining :app. We\'re excited to have you on board!', ['app' => $appName]) }}

@if($team)
<x-mail::panel>
**{{ __('Company') }}:** {{ $team->name }}  
**{{ __('Your Role') }}:** {{ __('Owner') }}
</x-mail::panel>
@endif

{{ __('Get started by exploring your dashboard and setting up your account.') }}

<x-mail::button :url="route('dashboard')">
{{ __('Go to Dashboard') }}
</x-mail::button>

{{ __('If you have any questions, feel free to reach out to our support team.') }}

{{ __('Thanks,') }}<br>
{{ $appName }}
</x-mail::message>

