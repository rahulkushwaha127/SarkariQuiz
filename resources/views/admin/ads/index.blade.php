@extends('layouts.admin')

@section('title', 'Ads')

@section('content')
    <div class="space-y-6">
        <div>
            <h1 class="text-2xl font-semibold tracking-tight text-slate-900">Ads</h1>
            <p class="mt-1 text-sm text-slate-600">
                Create AdSense units (unique snippets) and assign them to slots (header/footer). The app will prevent the same unit from rendering twice on one page.
            </p>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="text-base font-semibold text-slate-900">Slot assignments</div>
            <div class="mt-1 text-sm text-slate-600">Map each slot to an Ad Unit. Enable only what you want to show.</div>

            <div class="mt-4 overflow-hidden rounded-xl border border-slate-200">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200">
                        <thead class="bg-slate-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Slot</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Unit</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Enabled</th>
                            <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wider text-slate-500"></th>
                        </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                        @foreach($slots as $slot)
                            <tr class="hover:bg-slate-50">
                                <td class="px-4 py-3">
                                    <div class="font-medium text-slate-900">{{ $slot->name }}</div>
                                    <div class="text-xs text-slate-500">{{ $slot->key }} · {{ $slot->context }} · {{ $slot->type }}</div>
                                </td>
                                <td class="px-4 py-3">
                                    <form method="POST" action="{{ route('admin.ads.slots.update', $slot) }}" class="flex items-center gap-2">
                                        @csrf
                                        @method('PATCH')
                                        <select name="ad_unit_id" class="w-72 rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm">
                                            <option value="">— none —</option>
                                            @foreach($units as $u)
                                                <option value="{{ $u->id }}" @selected((int)($slot->assignment?->ad_unit_id) === (int)$u->id)>
                                                    {{ $u->name }} ({{ $u->key }}){{ $u->is_active ? '' : ' [inactive]' }}
                                                </option>
                                            @endforeach
                                        </select>
                                </td>
                                <td class="px-4 py-3">
                                    <label class="inline-flex items-center gap-2 text-sm text-slate-700">
                                        <input type="checkbox" name="enabled" value="1" class="h-4 w-4 rounded border-slate-300"
                                               @checked((bool)($slot->assignment?->enabled))>
                                        Enabled
                                    </label>
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <button class="rounded-xl bg-slate-900 px-3 py-2 text-xs font-semibold text-white hover:bg-slate-800">
                                        Save
                                    </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="text-base font-semibold text-slate-900">Create ad unit</div>
            <div class="mt-1 text-sm text-slate-600">Each unit should be a unique AdSense snippet (don’t reuse the same snippet for multiple slots on the same page).</div>

            <form method="POST" action="{{ route('admin.ads.units.store') }}" class="mt-4 grid gap-4 md:grid-cols-2">
                @csrf
                <div>
                    <label class="text-sm font-medium text-slate-700">Key (unique)</label>
                    <input name="key" value="{{ old('key') }}" placeholder="adsense_public_header_unit"
                           class="mt-1 w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm">
                </div>
                <div>
                    <label class="text-sm font-medium text-slate-700">Name</label>
                    <input name="name" value="{{ old('name') }}" placeholder="Public Header Unit"
                           class="mt-1 w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm">
                </div>
                <div>
                    <label class="text-sm font-medium text-slate-700">Provider</label>
                    <select name="provider" class="mt-1 w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm">
                        <option value="adsense" @selected(old('provider') === 'adsense')>AdSense</option>
                        <option value="custom" @selected(old('provider') === 'custom')>Custom</option>
                    </select>
                </div>
                <div class="flex items-end">
                    <label class="inline-flex items-center gap-2 text-sm text-slate-700">
                        <input type="checkbox" name="is_active" value="1" class="h-4 w-4 rounded border-slate-300"
                               @checked(old('is_active') == '1')>
                        Active
                    </label>
                </div>
                <div class="md:col-span-2">
                    <label class="text-sm font-medium text-slate-700">Ad code (HTML)</label>
                    <textarea name="code_html" rows="7"
                              class="mt-1 w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm font-mono">{{ old('code_html') }}</textarea>
                </div>
                <div class="md:col-span-2">
                    <button class="rounded-xl bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-500">
                        Create
                    </button>
                </div>
            </form>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="text-base font-semibold text-slate-900">Ad units</div>
            <div class="mt-1 text-sm text-slate-600">Edit code, enable/disable, or delete.</div>

            <div class="mt-4 space-y-4">
                @forelse($units as $u)
                    <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                        <div class="flex flex-wrap items-center justify-between gap-3">
                            <div>
                                <div class="font-semibold text-slate-900">{{ $u->name }}</div>
                                <div class="text-xs text-slate-500">{{ $u->key }} · {{ $u->provider }}</div>
                            </div>
                            <div class="flex items-center gap-2">
                                <form method="POST" action="{{ route('admin.ads.units.destroy', $u) }}"
                                      onsubmit="return confirm('Delete this ad unit?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-100">
                                        Delete
                                    </button>
                                </form>
                            </div>
                        </div>

                        <form method="POST" action="{{ route('admin.ads.units.update', $u) }}" class="mt-4 grid gap-3">
                            @csrf
                            @method('PATCH')

                            <div class="grid gap-3 md:grid-cols-3">
                                <div class="md:col-span-2">
                                    <label class="text-sm font-medium text-slate-700">Name</label>
                                    <input name="name" value="{{ old('name', $u->name) }}"
                                           class="mt-1 w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm">
                                </div>
                                <div>
                                    <label class="text-sm font-medium text-slate-700">Provider</label>
                                    <select name="provider" class="mt-1 w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm">
                                        <option value="adsense" @selected(old('provider', $u->provider) === 'adsense')>AdSense</option>
                                        <option value="custom" @selected(old('provider', $u->provider) === 'custom')>Custom</option>
                                    </select>
                                </div>
                            </div>

                            <label class="inline-flex items-center gap-2 text-sm text-slate-700">
                                <input type="checkbox" name="is_active" value="1" class="h-4 w-4 rounded border-slate-300"
                                       @checked(old('is_active', $u->is_active ? '1' : '0') == '1')>
                                Active
                            </label>

                            <div>
                                <label class="text-sm font-medium text-slate-700">Ad code (HTML)</label>
                                <textarea name="code_html" rows="7"
                                          class="mt-1 w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm font-mono">{{ old('code_html', $u->code_html) }}</textarea>
                            </div>

                            <div>
                                <button class="rounded-xl bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-800">
                                    Save changes
                                </button>
                            </div>
                        </form>
                    </div>
                @empty
                    <div class="text-sm text-slate-600">No ad units yet.</div>
                @endforelse
            </div>
        </div>
    </div>
@endsection

