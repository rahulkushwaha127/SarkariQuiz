@extends('layouts.admin')

@section('title', 'Edit Template: ' . $template->name)

@section('content')
<div class="space-y-6">
    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-semibold tracking-tight text-slate-900">{{ $template->name }}</h1>
            <p class="mt-1 text-sm text-slate-500">
                Key: <code class="rounded bg-slate-100 px-1.5 py-0.5 text-xs font-mono">{{ $template->key }}</code>
                @if($template->is_system) · <span class="text-amber-600 font-medium">System template</span> @endif
            </p>
        </div>
        <a href="{{ route('admin.notification-templates.index') }}"
           class="rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">
            &larr; Back
        </a>
    </div>

    <form method="POST" action="{{ route('admin.notification-templates.update', $template) }}" id="template-form">
        @csrf
        @method('PATCH')

        <div class="space-y-6">
            {{-- General info --}}
            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <h2 class="text-sm font-bold uppercase tracking-wider text-slate-400">General</h2>
                <div class="mt-4 grid gap-4 sm:grid-cols-2">
                    <div>
                        <label class="text-sm font-medium text-slate-700">Name</label>
                        <input type="text" name="name" value="{{ old('name', $template->name) }}"
                               class="mt-1 block w-full rounded-xl border border-slate-200 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        @error('name') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="text-sm font-medium text-slate-700">Status</label>
                        <div class="mt-2.5">
                            <label class="inline-flex items-center gap-2 text-sm">
                                <input type="hidden" name="is_active" value="0">
                                <input type="checkbox" name="is_active" value="1"
                                       {{ old('is_active', $template->is_active) ? 'checked' : '' }}
                                       class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                                <span class="text-slate-700">Active</span>
                            </label>
                        </div>
                    </div>
                    <div class="sm:col-span-2">
                        <label class="text-sm font-medium text-slate-700">Description</label>
                        <input type="text" name="description" value="{{ old('description', $template->description) }}"
                               class="mt-1 block w-full rounded-xl border border-slate-200 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                               placeholder="Short description for admin reference">
                    </div>
                </div>
            </div>

            {{-- Channels --}}
            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <h2 class="text-sm font-bold uppercase tracking-wider text-slate-400">Channels</h2>
                <p class="mt-1 text-xs text-slate-500">Select which channels this notification should be sent through.</p>
                <div class="mt-4 flex flex-wrap gap-4">
                    @foreach(['email' => 'Email', 'fcm' => 'FCM Push', 'in_app' => 'In-App'] as $ch => $label)
                        <label class="inline-flex items-center gap-2 rounded-xl border border-slate-200 px-4 py-2.5 text-sm font-medium has-[:checked]:border-indigo-300 has-[:checked]:bg-indigo-50">
                            <input type="checkbox" name="channels[]" value="{{ $ch }}"
                                   {{ in_array($ch, old('channels', $template->channels ?? [])) ? 'checked' : '' }}
                                   class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                            {{ $label }}
                        </label>
                    @endforeach
                </div>
            </div>

            {{-- Available variables --}}
            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <h2 class="text-sm font-bold uppercase tracking-wider text-slate-400">Available Variables</h2>
                <p class="mt-1 text-xs text-slate-500">Use these placeholders in your templates. Click to copy.</p>
                <div class="mt-3 flex flex-wrap gap-2">
                    @forelse($template->available_variables ?? [] as $var)
                        <button type="button"
                                class="var-copy-btn rounded-lg bg-slate-100 px-2.5 py-1 text-xs font-mono font-medium text-slate-700 hover:bg-indigo-100 hover:text-indigo-700 transition-colors"
                                data-var="@php echo '{{' . $var . '}}'; @endphp">
                            @{{ $var }}
                        </button>
                    @empty
                        <span class="text-xs text-slate-400">No variables defined.</span>
                    @endforelse
                </div>
            </div>

            {{-- Channel tabs --}}
            <div class="rounded-2xl border border-slate-200 bg-white shadow-sm">
                {{-- Tab navigation --}}
                <div class="flex border-b border-slate-200">
                    <button type="button" class="tab-btn px-5 py-3 text-sm font-semibold border-b-2 border-indigo-600 text-indigo-600" data-tab="email">
                        Email
                    </button>
                    <button type="button" class="tab-btn px-5 py-3 text-sm font-semibold border-b-2 border-transparent text-slate-500 hover:text-slate-700" data-tab="fcm">
                        FCM Push
                    </button>
                    <button type="button" class="tab-btn px-5 py-3 text-sm font-semibold border-b-2 border-transparent text-slate-500 hover:text-slate-700" data-tab="in_app">
                        In-App
                    </button>
                </div>

                {{-- Email tab --}}
                <div class="tab-panel p-5" data-panel="email">
                    <div class="space-y-4">
                        <div>
                            <label class="text-sm font-medium text-slate-700">Subject</label>
                            <input type="text" name="email_subject" value="{{ old('email_subject', $template->email_subject) }}"
                                   class="mt-1 block w-full rounded-xl border border-slate-200 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                   placeholder="e.g. Payment confirmed — ₹@{{amount}}">
                            @error('email_subject') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="text-sm font-medium text-slate-700">Body (HTML)</label>
                            <textarea name="email_body" id="email-body-editor" rows="12"
                                      class="mt-1 block w-full rounded-xl border border-slate-200 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('email_body', $template->email_body) }}</textarea>
                            @error('email_body') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>

                {{-- FCM tab --}}
                <div class="tab-panel hidden p-5" data-panel="fcm">
                    <div class="space-y-4">
                        <div>
                            <label class="text-sm font-medium text-slate-700">Title</label>
                            <input type="text" name="fcm_title" value="{{ old('fcm_title', $template->fcm_title) }}"
                                   class="mt-1 block w-full rounded-xl border border-slate-200 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                   placeholder="Push notification title">
                        </div>
                        <div>
                            <label class="text-sm font-medium text-slate-700">Body</label>
                            <textarea name="fcm_body" rows="4"
                                      class="mt-1 block w-full rounded-xl border border-slate-200 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                      placeholder="Push notification body">{{ old('fcm_body', $template->fcm_body) }}</textarea>
                        </div>
                        {{-- FCM preview --}}
                        <div class="rounded-xl bg-slate-50 p-4">
                            <div class="text-xs font-semibold uppercase text-slate-400">Push preview</div>
                            <div class="mt-2 flex items-start gap-3 rounded-xl bg-white p-3 shadow-sm border border-slate-200">
                                <div class="mt-0.5 flex h-8 w-8 items-center justify-center rounded-lg bg-indigo-100">
                                    <svg class="h-4 w-4 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0"/></svg>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <div class="text-sm font-semibold text-slate-900" id="fcm-preview-title">{{ $template->fcm_title ?: 'Title preview' }}</div>
                                    <div class="mt-0.5 text-xs text-slate-500 line-clamp-2" id="fcm-preview-body">{{ $template->fcm_body ?: 'Body preview' }}</div>
                                </div>
                                <div class="text-[10px] text-slate-400">now</div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- In-App tab --}}
                <div class="tab-panel hidden p-5" data-panel="in_app">
                    <div class="space-y-4">
                        <div>
                            <label class="text-sm font-medium text-slate-700">Title</label>
                            <input type="text" name="in_app_title" value="{{ old('in_app_title', $template->in_app_title) }}"
                                   class="mt-1 block w-full rounded-xl border border-slate-200 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                        <div>
                            <label class="text-sm font-medium text-slate-700">Body</label>
                            <textarea name="in_app_body" rows="4"
                                      class="mt-1 block w-full rounded-xl border border-slate-200 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('in_app_body', $template->in_app_body) }}</textarea>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-slate-700">URL (optional)</label>
                            <input type="text" name="in_app_url" value="{{ old('in_app_url', $template->in_app_url) }}"
                                   class="mt-1 block w-full rounded-xl border border-slate-200 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                   placeholder="e.g. /plans">
                        </div>
                    </div>
                </div>
            </div>

            {{-- Submit --}}
            <div class="flex items-center gap-3">
                <button type="submit"
                        class="rounded-xl bg-slate-900 px-6 py-2.5 text-sm font-semibold text-white hover:bg-slate-800">
                    Save Changes
                </button>
                <a href="{{ route('admin.notification-templates.index') }}"
                   class="rounded-xl border border-slate-200 bg-white px-6 py-2.5 text-sm font-medium text-slate-700 hover:bg-slate-50">
                    Cancel
                </a>
                <form method="POST" action="{{ route('admin.notification-templates.send-test', $template) }}" class="ml-auto">
                    @csrf
                    <button type="submit"
                            class="rounded-xl border border-indigo-200 bg-indigo-50 px-4 py-2.5 text-sm font-semibold text-indigo-700 hover:bg-indigo-100">
                        Send test to me
                    </button>
                </form>
            </div>
        </div>
    </form>

    {{-- Email preview panel --}}
    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm" id="email-preview-section">
        <div class="flex items-center justify-between border-b border-slate-200 px-5 py-3">
            <h2 class="text-sm font-bold uppercase tracking-wider text-slate-400">Email Preview</h2>
            <button type="button" id="refresh-preview-btn"
                    class="rounded-lg border border-slate-200 px-3 py-1.5 text-xs font-semibold text-slate-600 hover:bg-slate-50">
                Refresh preview
            </button>
        </div>
        <div class="p-5">
            <iframe id="email-preview-frame" class="w-full rounded-xl border border-slate-200" style="height: 400px;"></iframe>
        </div>
    </div>
</div>

@push('styles')
{{-- Summernote CSS --}}
<link href="https://cdn.jsdelivr.net/npm/summernote@0.9.0/dist/summernote-lite.min.css" rel="stylesheet">
<style>
    .note-editor.note-frame { border: 1px solid #e2e8f0 !important; border-radius: 12px !important; overflow: hidden; }
    .note-editor .note-toolbar { background: #f8fafc !important; border-bottom: 1px solid #e2e8f0 !important; padding: 6px 8px !important; }
    .note-editor .note-editing-area .note-editable { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; font-size: 14px; line-height: 1.7; color: #334155; padding: 16px !important; }
    .note-editor .note-statusbar { background: #f8fafc !important; border-top: 1px solid #e2e8f0 !important; }
    .note-btn { border-radius: 6px !important; }
</style>
@endpush

@push('scripts')
{{-- jQuery (required by Summernote) --}}
<script src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.min.js"></script>
{{-- Summernote JS --}}
<script src="https://cdn.jsdelivr.net/npm/summernote@0.9.0/dist/summernote-lite.min.js"></script>
<script>
(function() {
    /* ── Summernote ── */
    var $editor = $('#email-body-editor');
    $editor.summernote({
        height: 350,
        placeholder: 'Write your email body HTML here...',
        toolbar: [
            ['style', ['style']],
            ['font', ['bold', 'italic', 'underline', 'strikethrough', 'clear']],
            ['color', ['color']],
            ['para', ['ul', 'ol', 'paragraph']],
            ['table', ['table']],
            ['insert', ['link', 'picture', 'hr']],
            ['view', ['fullscreen', 'codeview', 'help']]
        ],
        callbacks: {
            onChange: function(contents) {
                // Keep textarea in sync for form submission
                $editor.val(contents);
            }
        }
    });

    /* ── Tab switching ── */
    document.querySelectorAll('.tab-btn').forEach(function(btn) {
        btn.addEventListener('click', function() {
            var tab = btn.getAttribute('data-tab');
            document.querySelectorAll('.tab-btn').forEach(function(b) {
                b.classList.remove('border-indigo-600', 'text-indigo-600');
                b.classList.add('border-transparent', 'text-slate-500');
            });
            btn.classList.add('border-indigo-600', 'text-indigo-600');
            btn.classList.remove('border-transparent', 'text-slate-500');

            document.querySelectorAll('.tab-panel').forEach(function(p) {
                p.classList.add('hidden');
            });
            document.querySelector('[data-panel="' + tab + '"]').classList.remove('hidden');
        });
    });

    /* ── Copy variable placeholder ── */
    document.querySelectorAll('.var-copy-btn').forEach(function(btn) {
        btn.addEventListener('click', function() {
            var v = btn.getAttribute('data-var');
            navigator.clipboard.writeText(v).then(function() {
                var orig = btn.textContent;
                btn.textContent = 'Copied!';
                btn.classList.add('bg-emerald-100', 'text-emerald-700');
                setTimeout(function() {
                    btn.textContent = orig;
                    btn.classList.remove('bg-emerald-100', 'text-emerald-700');
                }, 1200);
            });
        });
    });

    /* ── Live FCM preview ── */
    var fcmTitleInput = document.querySelector('input[name="fcm_title"]');
    var fcmBodyInput = document.querySelector('textarea[name="fcm_body"]');
    if (fcmTitleInput) {
        fcmTitleInput.addEventListener('input', function() {
            document.getElementById('fcm-preview-title').textContent = fcmTitleInput.value || 'Title preview';
        });
    }
    if (fcmBodyInput) {
        fcmBodyInput.addEventListener('input', function() {
            document.getElementById('fcm-preview-body').textContent = fcmBodyInput.value || 'Body preview';
        });
    }

    /* ── Email preview in iframe ── */
    function updateEmailPreview() {
        var frame = document.getElementById('email-preview-frame');
        var bodyContent = $editor.summernote('code');
        var html = buildPreviewHtml(bodyContent);
        frame.srcdoc = html;
    }

    var appName = @json(config('app.name', 'QuizWhiz'));

    function buildPreviewHtml(body) {
        return '<!DOCTYPE html><html><head><style>'
            + 'body{margin:0;padding:0;font-family:-apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,sans-serif;background:#f1f5f9;}'
            + '.c{max-width:560px;margin:24px auto;background:#fff;border-radius:16px;overflow:hidden;box-shadow:0 1px 3px rgba(0,0,0,.08);}'
            + '.h{background:linear-gradient(135deg,#4f46e5,#7c3aed);padding:24px 28px;text-align:center;color:#fff;font-size:18px;font-weight:700;}'
            + '.b{padding:24px 28px;font-size:14px;line-height:1.7;color:#334155;}'
            + '.b h2{font-size:18px;font-weight:600;color:#1e293b;margin:0 0 12px;}'
            + '.b p{margin:0 0 14px;}'
            + '.b .btn{display:inline-block;background:#4f46e5;color:#fff!important;text-decoration:none;padding:10px 24px;border-radius:10px;font-weight:600;font-size:13px;}'
            + '.b .highlight-box{background:#f8fafc;border:1px solid #e2e8f0;border-radius:10px;padding:14px;margin:14px 0;}'
            + '.b .amount{font-size:22px;font-weight:700;color:#1e293b;}'
            + '.f{padding:16px 28px;text-align:center;border-top:1px solid #f1f5f9;font-size:11px;color:#94a3b8;}'
            + '</style></head><body><div class="c">'
            + '<div class="h">' + appName + '</div>'
            + '<div class="b">' + body + '</div>'
            + '<div class="f">&copy; ' + new Date().getFullYear() + ' ' + appName + '. All rights reserved.</div>'
            + '</div></body></html>';
    }

    document.getElementById('refresh-preview-btn').addEventListener('click', updateEmailPreview);

    // Auto-load preview on page load
    setTimeout(updateEmailPreview, 500);
})();
</script>
@endpush
@endsection
