@extends('layouts.student')

@section('title', 'Contact')

@section('content')
<div class="space-y-4">
    <div class="rounded-2xl border border-white/10 bg-white/5 p-5 shadow-sm md:p-6">
        <h2 class="text-lg font-semibold text-white">Send Us a Message</h2>
        <form id="contact-form" class="mt-4 space-y-4" action="{{ route('public.contact.store') }}" method="POST">
            @csrf
            <div>
                <label for="contact-name" class="mb-1 block text-sm font-medium text-white">Your Name <span class="text-rose-400">*</span></label>
                <input type="text" id="contact-name" name="name" required
                       class="w-full rounded-xl border border-white/20 bg-white/10 px-4 py-3 text-sm text-white placeholder-slate-400 focus:border-violet-500 focus:outline-none focus:ring-1 focus:ring-violet-500"
                       placeholder="Your name" value="{{ old('name') }}">
                <p id="contact-name-error" class="mt-1 text-xs text-rose-400 hidden"></p>
            </div>
            <div>
                <label for="contact-email" class="mb-1 block text-sm font-medium text-white">Your Email <span class="text-rose-400">*</span></label>
                <input type="email" id="contact-email" name="email" required
                       class="w-full rounded-xl border border-white/20 bg-white/10 px-4 py-3 text-sm text-white placeholder-slate-400 focus:border-violet-500 focus:outline-none focus:ring-1 focus:ring-violet-500"
                       placeholder="your@email.com" value="{{ old('email') }}">
                <p id="contact-email-error" class="mt-1 text-xs text-rose-400 hidden"></p>
            </div>
            <div>
                <label for="contact-subject" class="mb-1 block text-sm font-medium text-white">Subject <span class="text-rose-400">*</span></label>
                <input type="text" id="contact-subject" name="subject" required
                       class="w-full rounded-xl border border-white/20 bg-white/10 px-4 py-3 text-sm text-white placeholder-slate-400 focus:border-violet-500 focus:outline-none focus:ring-1 focus:ring-violet-500"
                       placeholder="Subject" value="{{ old('subject') }}">
                <p id="contact-subject-error" class="mt-1 text-xs text-rose-400 hidden"></p>
            </div>
            <div>
                <label for="contact-message" class="mb-1 block text-sm font-medium text-white">Message <span class="text-rose-400">*</span></label>
                <textarea id="contact-message" name="message" required rows="5"
                          class="w-full resize-y rounded-xl border border-white/20 bg-white/10 px-4 py-3 text-sm text-white placeholder-slate-400 focus:border-violet-500 focus:outline-none focus:ring-1 focus:ring-violet-500"
                          placeholder="Your message">{{ old('message') }}</textarea>
                <p id="contact-message-error" class="mt-1 text-xs text-rose-400 hidden"></p>
            </div>
            <p id="contact-form-error" class="text-sm text-rose-400 hidden"></p>
            <p id="contact-form-success" class="text-sm text-emerald-400 hidden"></p>
            <button type="submit" id="contact-submit" class="w-full rounded-xl bg-violet-600 px-4 py-3 text-sm font-semibold text-white hover:bg-violet-500 focus:outline-none focus:ring-2 focus:ring-violet-500 focus:ring-offset-2 focus:ring-offset-slate-950 disabled:opacity-60">
                Send Message
            </button>
        </form>
    </div>

    <div class="border border-white/10 bg-white/5 p-4 rounded-xl">
        <div class="text-sm text-slate-300">
            @include('shared.pages.contact')
        </div>
    </div>
</div>

<script>
(function () {
    const form = document.getElementById('contact-form');
    if (!form) return;

    const submitBtn = document.getElementById('contact-submit');
    const fields = ['name', 'email', 'subject', 'message'];
    const successEl = document.getElementById('contact-form-success');
    const errorEl = document.getElementById('contact-form-error');

    function clearFieldErrors() {
        fields.forEach(function (f) {
            const el = document.getElementById('contact-' + f + '-error');
            if (el) { el.classList.add('hidden'); el.textContent = ''; }
        });
        if (errorEl) { errorEl.classList.add('hidden'); errorEl.textContent = ''; }
        if (successEl) { successEl.classList.add('hidden'); successEl.textContent = ''; }
    }

    form.addEventListener('submit', function (e) {
        e.preventDefault();
        clearFieldErrors();
        if (submitBtn) submitBtn.disabled = true;

        const body = new FormData(form);
        const token = document.querySelector('meta[name="csrf-token"]');
        const headers = { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' };
        if (token) headers['X-CSRF-TOKEN'] = token.getAttribute('content');

        fetch(form.action, { method: 'POST', body, headers })
            .then(function (res) { return res.json().then(function (data) { return { ok: res.ok, status: res.status, data }; }); })
            .then(function (result) {
                if (result.ok) {
                    if (successEl) {
                        successEl.textContent = result.data.message || 'Message sent successfully.';
                        successEl.classList.remove('hidden');
                    }
                    form.reset();
                } else {
                    if (result.data.errors) {
                        Object.keys(result.data.errors).forEach(function (key) {
                            const errEl = document.getElementById('contact-' + key + '-error');
                            if (errEl) {
                                errEl.textContent = result.data.errors[key][0] || '';
                                errEl.classList.remove('hidden');
                            }
                        });
                    }
                    if (errorEl) {
                        errorEl.textContent = result.data.message || 'Something went wrong. Please try again.';
                        errorEl.classList.remove('hidden');
                    }
                }
            })
            .catch(function () {
                if (errorEl) {
                    errorEl.textContent = 'Something went wrong. Please try again.';
                    errorEl.classList.remove('hidden');
                }
            })
            .finally(function () {
                if (submitBtn) submitBtn.disabled = false;
            });
    });
})();
</script>
@endsection
