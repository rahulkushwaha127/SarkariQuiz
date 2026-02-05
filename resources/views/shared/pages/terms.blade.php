@php
    $siteName = $siteName ?? config('app.name', 'Quizeking');
    $siteUrl = config('app.url', 'https://quizeking.com');
@endphp

<div class="space-y-4">
    <p><strong>Last updated:</strong> {{ now()->format('F j, Y') }}</p>

    <p>
        Welcome to {{ $siteName }}. By accessing or using the website and services at {{ $siteUrl }} (the “Service”), you agree to be bound by these Terms and Conditions (“Terms”). If you do not agree, do not use the Service.
    </p>

    <h3 class="mt-4 font-semibold text-white">1. Acceptance of Terms</h3>
    <p>
        By creating an account, playing quizzes, or otherwise using the Service, you confirm that you have read, understood, and agree to these Terms and to our <a href="{{ route('public.pages.privacy') }}" class="text-amber-400 hover:underline">Privacy Policy</a>.
    </p>

    <h3 class="mt-4 font-semibold text-white">2. Use of the Service</h3>
    <p>
        The Service is provided for personal, non-commercial use. You may use it to take quizzes, practice, participate in contests and daily challenges (where available), and related features. You must not use the Service for any illegal purpose, to harass others, to cheat (e.g. automated scripts, multiple accounts to gain unfair advantage), to spam, or to attempt to gain unauthorized access to our or others’ systems or data.
    </p>

    <h3 class="mt-4 font-semibold text-white">3. Account and Registration</h3>
    <p>
        You may need to register to access certain features. You agree to provide accurate information and to keep your account credentials secure. You are responsible for all activity under your account. We may suspend or terminate accounts that violate these Terms or for other operational or legal reasons.
    </p>

    <h3 class="mt-4 font-semibold text-white">4. Conduct</h3>
    <p>
        You agree not to abuse the platform. This includes, but is not limited to: spamming, cheating, using automation or bots, impersonating others, posting harmful or offensive content, or interfering with the Service’s operation. We may remove content and suspend or terminate access at our discretion.
    </p>

    <h3 class="mt-4 font-semibold text-white">5. Content and Intellectual Property</h3>
    <p>
        Quizzes, questions, and other content on the Service are owned by us or our licensors (including creators). You may view and use them only as permitted by the Service (e.g. taking quizzes for personal learning). You may not copy, scrape, redistribute, or create derivative works from our content without permission. Trademarks and branding associated with {{ $siteName }} are our property.
    </p>

    <h3 class="mt-4 font-semibold text-white">6. Disclaimer of Warranties</h3>
    <p>
        The Service is provided “as is” and “as available.” We do not warrant that the Service will be uninterrupted, error-free, or free of harmful components. We disclaim all warranties, express or implied, to the fullest extent permitted by law.
    </p>

    <h3 class="mt-4 font-semibold text-white">7. Limitation of Liability</h3>
    <p>
        To the maximum extent permitted by law, {{ $siteName }} and its operators shall not be liable for any indirect, incidental, special, consequential, or punitive damages, or for any loss of profits, data, or goodwill, arising from your use or inability to use the Service. Our total liability shall not exceed the amount you paid us, if any, in the twelve months preceding the claim, or one hundred dollars, whichever is less.
    </p>

    <h3 class="mt-4 font-semibold text-white">8. Termination</h3>
    <p>
        We may terminate or suspend your access to the Service at any time, with or without cause or notice. You may stop using the Service at any time. Provisions that by their nature should survive (including disclaimers, limitations of liability, and governing law) will survive termination.
    </p>

    <h3 class="mt-4 font-semibold text-white">9. Changes to the Terms</h3>
    <p>
        We may update these Terms from time to time. We will post the updated Terms on this page and update the “Last updated” date. Your continued use of the Service after changes constitutes acceptance. If you do not agree, you must stop using the Service.
    </p>

    <h3 class="mt-4 font-semibold text-white">10. Governing Law</h3>
    <p>
        These Terms shall be governed by the laws of India, without regard to conflict of law principles. Any disputes shall be subject to the exclusive jurisdiction of the courts of India.
    </p>

    <h3 class="mt-4 font-semibold text-white">11. Contact</h3>
    <p>
        For questions about these Terms, please contact us via our <a href="{{ route('public.pages.contact') }}" class="text-amber-400 hover:underline">Contact</a> page or the contact details provided for {{ $siteName }}.
    </p>
</div>
