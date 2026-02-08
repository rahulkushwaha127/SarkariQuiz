@php $isEdit = $plan->exists; @endphp

<form method="POST" action="{{ $isEdit ? route('admin.plans.update', $plan) : route('admin.plans.store') }}" class="space-y-4">
    @csrf
    @if($isEdit) @method('PATCH') @endif

    <div class="grid gap-4 sm:grid-cols-2">
        <div class="sm:col-span-2">
            <label class="block text-sm font-medium text-slate-700">Plan name <span class="text-red-500">*</span></label>
            <input name="name" value="{{ old('name', $plan->name) }}" required
                   class="mt-1 w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-900 shadow-sm focus:border-slate-400 focus:outline-none"
                   placeholder="e.g. Free, Pro, Enterprise">
        </div>

        <div>
            <label class="block text-sm font-medium text-slate-700">Duration <span class="text-red-500">*</span></label>
            <select name="duration" required
                    class="mt-1 w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-900 shadow-sm focus:border-slate-400 focus:outline-none">
                <option value="weekly" @selected(old('duration', $plan->duration ?? 'monthly') === 'weekly')>Weekly</option>
                <option value="monthly" @selected(old('duration', $plan->duration ?? 'monthly') === 'monthly')>Monthly</option>
                <option value="yearly" @selected(old('duration', $plan->duration ?? 'monthly') === 'yearly')>Yearly</option>
            </select>
            <div class="mt-1 text-xs text-slate-500">Billing cycle for this plan.</div>
        </div>

        <div>
            <label class="block text-sm font-medium text-slate-700">Price label</label>
            <input name="price_label" value="{{ old('price_label', $plan->price_label) }}"
                   class="mt-1 w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-900 shadow-sm focus:border-slate-400 focus:outline-none"
                   placeholder="e.g. Free, Rs 499/mo">
            <div class="mt-1 text-xs text-slate-500">For display only, not billing.</div>
        </div>

        <div>
            <label class="block text-sm font-medium text-slate-700">Sort order</label>
            <input name="sort_order" type="number" min="0" value="{{ old('sort_order', $plan->sort_order ?? 0) }}"
                   class="mt-1 w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-900 shadow-sm focus:border-slate-400 focus:outline-none">
        </div>

        <div class="sm:col-span-2">
            <label class="block text-sm font-medium text-slate-700">Description</label>
            <textarea name="description" rows="2"
                      class="mt-1 w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-900 shadow-sm focus:border-slate-400 focus:outline-none"
                      placeholder="Short description shown to creators">{{ old('description', $plan->description) }}</textarea>
        </div>
    </div>

    <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
        <div class="text-sm font-semibold text-slate-900">Limits</div>
        <p class="mt-1 text-xs text-slate-500">Leave blank for unlimited.</p>

        <div class="mt-3 grid gap-4 sm:grid-cols-2">
            <div>
                <label class="block text-sm font-medium text-slate-700">Max quizzes</label>
                <input name="max_quizzes" type="number" min="0" value="{{ old('max_quizzes', $plan->max_quizzes) }}"
                       class="mt-1 w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-900 shadow-sm focus:border-slate-400 focus:outline-none"
                       placeholder="Unlimited">
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700">Max batches</label>
                <input name="max_batches" type="number" min="0" value="{{ old('max_batches', $plan->max_batches) }}"
                       class="mt-1 w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-900 shadow-sm focus:border-slate-400 focus:outline-none"
                       placeholder="Unlimited">
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700">Max students per batch</label>
                <input name="max_students_per_batch" type="number" min="0" value="{{ old('max_students_per_batch', $plan->max_students_per_batch) }}"
                       class="mt-1 w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-900 shadow-sm focus:border-slate-400 focus:outline-none"
                       placeholder="Unlimited">
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700">Max AI generations / month</label>
                <input name="max_ai_generations_per_month" type="number" min="0" value="{{ old('max_ai_generations_per_month', $plan->max_ai_generations_per_month) }}"
                       class="mt-1 w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-900 shadow-sm focus:border-slate-400 focus:outline-none"
                       placeholder="Unlimited">
            </div>
        </div>
    </div>

    <div class="rounded-2xl border border-slate-200 bg-white p-4 space-y-3">
        <div class="text-sm font-semibold text-slate-900">Features</div>

        <label class="flex items-center gap-2">
            <input type="hidden" name="can_access_question_bank" value="0">
            <input type="checkbox" name="can_access_question_bank" value="1"
                   class="h-4 w-4 rounded border-slate-300 text-slate-900 focus:ring-slate-900"
                   @checked(old('can_access_question_bank', $plan->can_access_question_bank))>
            <span class="text-sm text-slate-700">Access shared question bank</span>
        </label>

        <label class="flex items-center gap-2">
            <input type="hidden" name="is_default" value="0">
            <input type="checkbox" name="is_default" value="1"
                   class="h-4 w-4 rounded border-slate-300 text-slate-900 focus:ring-slate-900"
                   @checked(old('is_default', $plan->is_default))>
            <span class="text-sm text-slate-700">Default plan (auto-assigned to new creators)</span>
        </label>

        <label class="flex items-center gap-2">
            <input type="hidden" name="is_active" value="0">
            <input type="checkbox" name="is_active" value="1"
                   class="h-4 w-4 rounded border-slate-300 text-slate-900 focus:ring-slate-900"
                   @checked(old('is_active', $plan->exists ? $plan->is_active : true))>
            <span class="text-sm text-slate-700">Active</span>
        </label>
    </div>

    <div class="flex items-center justify-end gap-2">
        <button type="submit" class="rounded-xl bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-800">
            {{ $isEdit ? 'Save' : 'Create' }}
        </button>
    </div>
</form>
