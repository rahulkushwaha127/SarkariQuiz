@php $isEdit = $plan->exists; @endphp

<form method="POST" action="{{ $isEdit ? route('admin.student-plans.update', $plan) : route('admin.student-plans.store') }}" class="space-y-4">
    @csrf
    @if($isEdit) @method('PATCH') @endif

    <div class="grid gap-4 sm:grid-cols-2">
        <div class="sm:col-span-2">
            <label class="block text-sm font-medium text-slate-700">Plan name <span class="text-red-500">*</span></label>
            <input name="name" value="{{ old('name', $plan->name) }}" required
                   class="mt-1 w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-900 shadow-sm focus:border-slate-400 focus:outline-none"
                   placeholder="e.g. Free, Premium, Pro">
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
                   placeholder="e.g. Free, ₹99/month">
            <div class="mt-1 text-xs text-slate-500">Shown on student pricing page.</div>
        </div>

        <div>
            <label class="block text-sm font-medium text-slate-700">Price (₹)</label>
            <input name="price_rupees" type="number" min="0" step="0.01" value="{{ old('price_rupees', $plan->price_paise !== null ? $plan->price_paise / 100 : '') }}"
                   class="mt-1 w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-900 shadow-sm focus:border-slate-400 focus:outline-none"
                   placeholder="0 = free, e.g. 99">
            <div class="mt-1 text-xs text-slate-500">Leave 0 or blank for free. Enter amount in rupees (e.g. 99 for ₹99).</div>
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
                      placeholder="Short description for students">{{ old('description', $plan->description) }}</textarea>
        </div>
    </div>

    <div class="rounded-2xl border border-slate-200 bg-white p-4">
        <label class="flex items-center gap-2">
            <input type="hidden" name="is_active" value="0">
            <input type="checkbox" name="is_active" value="1"
                   class="h-4 w-4 rounded border-slate-300 text-slate-900 focus:ring-slate-900"
                   @checked(old('is_active', $plan->exists ? $plan->is_active : true))>
            <span class="text-sm text-slate-700">Active (show on student pricing page)</span>
        </label>
    </div>

    <div class="flex items-center justify-end gap-2">
        <button type="submit" class="rounded-xl bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-800">
            {{ $isEdit ? 'Save' : 'Create' }}
        </button>
    </div>
</form>
