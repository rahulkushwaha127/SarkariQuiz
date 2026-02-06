@extends('layouts.admin')

@section('title', 'Plans')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <h1 class="text-2xl font-semibold tracking-tight text-slate-900">Plans</h1>
            <p class="mt-1 text-sm text-slate-600">Creator subscription plans with configurable limits. Assign plans to creators from the Users page.</p>
        </div>

        <a href="#"
           data-ajax-modal="true"
           data-title="Create plan"
           data-size="md"
           data-url="{{ route('admin.plans.create') }}"
           class="rounded-xl bg-slate-900 px-4 py-2 text-center text-sm font-semibold text-white hover:bg-slate-800">
            Create plan
        </a>
    </div>

    @if(session('error'))
        <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-2 text-sm text-red-800">{{ session('error') }}</div>
    @endif

    @if($plans->isEmpty())
        <div class="rounded-2xl border border-slate-200 bg-white p-8 text-center shadow-sm">
            <p class="text-sm text-slate-600">No plans created yet. Create one to start limiting or unlocking creator features.</p>
        </div>
    @else
        <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Plan</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Quizzes</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Batches</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Students/Batch</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">AI/Month</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Q-Bank</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Users</th>
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
                                            @if($plan->price_label)
                                                <div class="text-xs text-slate-500">{{ $plan->price_label }}</div>
                                            @endif
                                        </div>
                                        @if($plan->is_default)
                                            <span class="rounded-full bg-indigo-100 px-2 py-0.5 text-[10px] font-bold uppercase text-indigo-700">Default</span>
                                        @endif
                                        @unless($plan->is_active)
                                            <span class="rounded-full bg-slate-100 px-2 py-0.5 text-[10px] font-bold uppercase text-slate-500">Inactive</span>
                                        @endunless
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-sm text-slate-700">{{ $plan->limitLabel('max_quizzes') }}</td>
                                <td class="px-4 py-3 text-sm text-slate-700">{{ $plan->limitLabel('max_batches') }}</td>
                                <td class="px-4 py-3 text-sm text-slate-700">{{ $plan->limitLabel('max_students_per_batch') }}</td>
                                <td class="px-4 py-3 text-sm text-slate-700">{{ $plan->limitLabel('max_ai_generations_per_month') }}</td>
                                <td class="px-4 py-3 text-sm">
                                    @if($plan->can_access_question_bank)
                                        <span class="text-emerald-600 font-semibold">Yes</span>
                                    @else
                                        <span class="text-slate-400">No</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-sm text-slate-700">
                                    {{ \App\Models\User::where('plan_id', $plan->id)->count() }}
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="#"
                                           data-ajax-modal="true"
                                           data-title="Edit plan: {{ $plan->name }}"
                                           data-size="md"
                                           data-url="{{ route('admin.plans.edit', $plan) }}"
                                           class="rounded-xl border border-slate-200 bg-white px-3 py-1.5 text-xs font-semibold text-slate-700 hover:bg-slate-50">
                                            Edit
                                        </a>
                                        <button type="button"
                                                class="rounded-xl border border-red-200 bg-white px-3 py-1.5 text-xs font-semibold text-red-600 hover:bg-red-50"
                                                data-delete-trigger="{{ route('admin.plans.destroy', $plan) }}">
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
