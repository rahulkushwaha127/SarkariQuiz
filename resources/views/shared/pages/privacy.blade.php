@php
    $siteName = $siteName ?? config('app.name', 'Quizeking');
    $siteUrl = config('app.url', 'https://quizeking.com');
@endphp

<div class="space-y-4">
    <p><strong>Last updated:</strong> {{ now()->format('F j, Y') }}</p>

    <p>
        {{ $siteName }} (“we”, “our”, or “us”) operates the website and services at {{ $siteUrl }} (the “Service”). This Privacy Policy explains how we collect, use, disclose, and safeguard your information when you use our Service.
    </p>

    <h3 class="mt-4 font-semibold text-white">1. Information We Collect</h3>
    <p>
        We may collect information that you provide directly (e.g. when you register, contact us, or use the Service), including: name, email address, profile information, and any content you submit. We may also collect device and usage information such as IP address, browser type, device identifiers, and how you use the Service (e.g. quizzes attempted, scores). If you opt in to push notifications, we store device tokens (e.g. FCM) to deliver them.
    </p>

    <h3 class="mt-4 font-semibold text-white">2. How We Use Your Information</h3>
    <p>
        We use the information to provide, maintain, and improve the Service; to personalize your experience; to send you notifications you have requested; to communicate with you; to detect and prevent abuse or fraud; and to comply with legal obligations.
    </p>

    <h3 class="mt-4 font-semibold text-white">3. Cookies and Similar Technologies</h3>
    <p>
        We use cookies and similar technologies for session management, preferences, security, and analytics. For more detail, see our <a href="{{ route('public.pages.cookie') }}" class="text-amber-400 hover:underline">Cookie Policy</a>.
    </p>

    <h3 class="mt-4 font-semibold text-white">4. Sharing and Disclosure</h3>
    <p>
        We may share your information with service providers who assist us (e.g. hosting, analytics, push notifications), and when required by law or to protect our rights. We do not sell your personal information to third parties for their marketing.
    </p>

    <h3 class="mt-4 font-semibold text-white">5. Data Retention</h3>
    <p>
        We retain your data for as long as your account is active or as needed to provide the Service, and for a reasonable period thereafter for legal, safety, and operational purposes.
    </p>

    <h3 class="mt-4 font-semibold text-white">6. Your Rights</h3>
    <p>
        Depending on your location, you may have rights to access, correct, delete, or restrict processing of your personal data, or to data portability. You can update account details in your profile and contact us for other requests.
    </p>

    <h3 class="mt-4 font-semibold text-white">7. Children’s Privacy</h3>
    <p>
        Our Service is not directed at children under 13. We do not knowingly collect personal information from children under 13. If you believe we have collected such information, please contact us so we can delete it.
    </p>

    <h3 class="mt-4 font-semibold text-white">8. Security</h3>
    <p>
        We use reasonable technical and organizational measures to protect your information. No method of transmission or storage is 100% secure; we cannot guarantee absolute security.
    </p>

    <h3 class="mt-4 font-semibold text-white">9. International Transfers</h3>
    <p>
        Your information may be processed in countries other than your own. We take steps to ensure appropriate safeguards where required.
    </p>

    <h3 class="mt-4 font-semibold text-white">10. Changes to This Policy</h3>
    <p>
        We may update this Privacy Policy from time to time. We will post the updated policy on this page and update the “Last updated” date. Continued use of the Service after changes constitutes acceptance.
    </p>

    <h3 class="mt-4 font-semibold text-white">11. Contact Us</h3>
    <p>
        For privacy-related questions or to exercise your rights, contact us at the address or form provided on our <a href="{{ route('public.pages.contact') }}" class="text-amber-400 hover:underline">Contact</a> page, or at the email address listed for {{ $siteName }}.
    </p>
</div>
