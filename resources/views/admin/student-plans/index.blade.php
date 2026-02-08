@extends('layouts.admin')

@section('title', 'Student Plans')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <h1 class="text-2xl font-semibold tracking-tight text-slate-900">Student plans</h1>
            <p class="mt-1 text-sm text-slate-600">Subscription tiers for students (Free, Premium, etc.). Shown on student pricing page; assign from Users.</p>
        </div>

        <a href="#"
           data-ajax-modal="true"
           data-title="Create student plan"
           data-size="md"
           data-url="{{ route('admin.student-plans.create') }}"
           class="rounded-xl bg-slate-900 px-4 py-2 text-center text-sm font-semibold text-white hover:bg-slate-800">
            Create student plan
        </a>
    </div>

    @if(session('error'))
        <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-2 text-sm text-red-800">{{ session('error') }}</div>
    @endif

    @if($plans->isEmpty())
        <div class="rounded-2xl border border-slate-200 bg-white p-8 text-center shadow-sm">
            <p class="text-sm text-slate-600">No student plans yet. Create one (e.g. Free, Premium) to show on the student pricing page.</p>
        </div>
    @else
        <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Plan</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Duration</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Price</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Students</th>
                            <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wider text-slate-500">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach ($plans as $plan)
                            <tr class="hover:bg-slate-50">
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-2">
                                        <div>
                                            <div class="font-medium text-slate-900">{{ $plan->name }}</div>
                                            @if($plan->description)
                                                <div class="text-xs text-slate-500">{{ Str::limit($plan->description, 50) }}</div>
                                            @endif
                                        </div>
                                        @unless($plan->is_active)
                                            <span class="rounded-full bg-slate-100 px-2 py-0.5 text-[10px] font-bold uppercase text-slate-500">Inactive</span>
                                        @endunless
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-sm text-slate-700">
                                    <span class="rounded-full bg-indigo-50 px-2 py-0.5 text-xs font-semibold text-indigo-700">{{ $plan->durationLabel() }}</span>
                                </td>
                                <td class="px-4 py-3 text-sm text-slate-700">
                                    @if($plan->isFree())
                                        <span class="text-slate-500">Free</span>
                                    @else
                                        {{ $plan->price_label ?: '₹' . number_format($plan->priceInRupees(), 0) . $plan->durationSuffix() }}
                                        <span class="text-slate-400">({{ $plan->price_paise }} paise)</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-sm text-slate-700">
                                    {{ \App\Models\User::where('student_plan_id', $plan->id)->count() }}
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="#"
                                           data-ajax-modal="true"
                                           data-title="Edit student plan: {{ $plan->name }}"
                                           data-size="md"
                                           data-url="{{ route('admin.student-plans.edit', $plan) }}"
                                           class="rounded-xl border border-slate-200 bg-white px-3 py-1.5 text-xs font-semibold text-slate-700 hover:bg-slate-50">
                                            Edit
                                        </a>
                                        <button type="button"
                                                class="rounded-xl border border-red-200 bg-white px-3 py-1.5 text-xs font-semibold text-red-600 hover:bg-red-50"
                                                data-delete-trigger="{{ route('admin.student-plans.destroy', $plan) }}">
                                            Delete
                                        </button>
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
