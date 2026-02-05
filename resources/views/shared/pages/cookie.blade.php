@php
    $siteName = $siteName ?? config('app.name', 'Quizeking');
@endphp

<div class="space-y-4">
    <p><strong>Last updated:</strong> {{ now()->format('F j, Y') }}</p>

    <p>
        This Cookie Policy explains how {{ $siteName }} (“we”, “our”, or “us”) uses cookies and similar technologies when you use our website and services.
    </p>

    <h3 class="mt-4 font-semibold text-white">What Are Cookies?</h3>
    <p>
        Cookies are small text files that are stored on your device when you visit a website. They are widely used to make websites work properly, to remember your preferences, and to understand how visitors use the site.
    </p>

    <h3 class="mt-4 font-semibold text-white">How We Use Cookies</h3>
    <p>
        We use cookies and similar technologies (e.g. local storage, session storage) for the following purposes:
    </p>
    <ul class="list-disc list-inside space-y-1 ml-2">
        <li><strong>Essential:</strong> Required for the site to function (e.g. session management, login state, security). These cannot be disabled if you want to use the Service.</li>
        <li><strong>Preferences:</strong> To remember your settings (e.g. theme, language) and improve your experience.</li>
        <li><strong>Analytics and performance:</strong> To understand how visitors use our site (e.g. pages visited, features used) so we can improve the Service. We may use first-party or third-party tools for this.</li>
    </ul>

    <h3 class="mt-4 font-semibold text-white">Managing Cookies</h3>
    <p>
        You can control or delete cookies through your browser settings. Most browsers allow you to refuse or accept cookies, or to delete existing cookies. Disabling certain cookies may affect how the site works (e.g. you may need to log in again each time, or some features may not work as intended).
    </p>

    <h3 class="mt-4 font-semibold text-white">Third-Party Cookies</h3>
    <p>
        We may allow third-party services (e.g. analytics, push notification providers) to set cookies or similar technologies when you use our Service. Their use of data is governed by their own privacy policies.
    </p>

    <h3 class="mt-4 font-semibold text-white">Updates</h3>
    <p>
        We may update this Cookie Policy from time to time. We will post the updated policy on this page and update the “Last updated” date. Continued use of the Service after changes constitutes acceptance.
    </p>

    <h3 class="mt-4 font-semibold text-white">Contact</h3>
    <p>
        For questions about our use of cookies, please contact us via our <a href="{{ route('public.pages.contact') }}" class="text-amber-400 hover:underline">Contact</a> page. For more on how we process personal data, see our <a href="{{ route('public.pages.privacy') }}" class="text-amber-400 hover:underline">Privacy Policy</a>.
    </p>
</div>
