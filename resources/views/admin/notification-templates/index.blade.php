@extends('layouts.admin')

@section('title', 'Notification Templates')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <h1 class="text-2xl font-semibold tracking-tight text-slate-900">Notification Templates</h1>
            <p class="mt-1 text-sm text-slate-600">Manage the content and channels for all automated notifications.</p>
        </div>
    </div>

    @if($templates->isEmpty())
        <div class="rounded-2xl border border-slate-200 bg-white p-8 text-center shadow-sm">
            <p class="text-sm text-slate-600">No templates found. Run the seeder to create defaults.</p>
        </div>
    @else
        <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Template</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Key</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Channels</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Status</th>
                            <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wider text-slate-500">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($templates as $tpl)
                            <tr class="hover:bg-slate-50">
                                <td class="px-4 py-3">
                                    <div class="font-medium text-slate-900">{{ $tpl->name }}</div>
                                    @if($tpl->description)
                                        <div class="text-xs text-slate-500">{{ Str::limit($tpl->description, 60) }}</div>
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    <span class="rounded-full bg-slate-100 px-2 py-0.5 text-xs font-mono font-medium text-slate-600">{{ $tpl->key }}</span>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex flex-wrap gap-1">
                                        @foreach($tpl->channels ?? [] as $ch)
                                            @php
                                                $colors = match($ch) {
                                                    'email'  => 'bg-blue-100 text-blue-700',
                                                    'fcm'    => 'bg-amber-100 text-amber-700',
                                                    'in_app' => 'bg-purple-100 text-purple-700',
                                                    default  => 'bg-slate-100 text-slate-600',
                                                };
                                                $labels = match($ch) {
                                                    'email'  => 'Email',
                                                    'fcm'    => 'Push',
                                                    'in_app' => 'In-App',
                                                    default  => $ch,
                                                };
                                            @endphp
                                            <span class="rounded-full px-2 py-0.5 text-xs font-semibold {{ $colors }}">{{ $labels }}</span>
                                        @endforeach
                                        @if(empty($tpl->channels))
                                            <span class="text-sm text-slate-400">&mdash;</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-sm">
                                    @if($tpl->is_active)
                                        <span class="rounded-full bg-emerald-100 px-2 py-0.5 text-xs font-semibold text-emerald-800">Active</span>
                                    @else
                                        <span class="rounded-full bg-slate-100 px-2 py-0.5 text-xs font-semibold text-slate-500">Inactive</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="{{ route('admin.notification-templates.edit', $tpl) }}"
                                           class="rounded-xl border border-slate-200 bg-white px-3 py-1.5 text-xs font-semibold text-slate-700 hover:bg-slate-50">
                                            Edit
                                        </a>
                                        <form method="POST" action="{{ route('admin.notification-templates.send-test', $tpl) }}" class="inline">
                                            @csrf
                                            <button type="submit"
                                                    class="rounded-xl border border-indigo-200 bg-indigo-50 px-3 py-1.5 text-xs font-semibold text-indigo-700 hover:bg-indigo-100">
                                                Test
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</div>
@endsection
