<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdSlot;
use App\Models\AdSlotAssignment;
use App\Models\AdUnit;
use App\Services\Ads\AdService;
use Illuminate\Http\Request;

class AdsController extends Controller
{
    public function index()
    {
        $slots = AdSlot::query()
            ->orderBy('context')
            ->orderBy('type')
            ->orderBy('key')
            ->with(['assignment.adUnit'])
            ->get();

        $units = AdUnit::query()
            ->orderBy('is_active', 'desc')
            ->orderBy('name')
            ->get();

        return view('admin.ads.index', compact('slots', 'units'));
    }

    public function storeUnit(Request $request)
    {
        $data = $request->validate([
            'key' => ['required', 'string', 'max:80', 'regex:/^[a-z0-9_]+$/', 'unique:ad_units,key'],
            'name' => ['required', 'string', 'max:120'],
            'provider' => ['required', 'string', 'in:adsense,custom'],
            'code_html' => ['required', 'string', 'max:100000'],
            'is_active' => ['nullable', 'in:0,1'],
        ]);

        AdUnit::query()->create([
            'key' => $data['key'],
            'name' => $data['name'],
            'provider' => $data['provider'],
            'code_html' => $data['code_html'],
            'is_active' => (bool) ((int) ($data['is_active'] ?? 0)),
        ]);

        AdService::forgetCache();

        return redirect()->route('admin.ads.index')->with('status', 'Ad unit created.');
    }

    public function updateUnit(Request $request, AdUnit $unit)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'provider' => ['required', 'string', 'in:adsense,custom'],
            'code_html' => ['required', 'string', 'max:100000'],
            'is_active' => ['nullable', 'in:0,1'],
        ]);

        $unit->update([
            'name' => $data['name'],
            'provider' => $data['provider'],
            'code_html' => $data['code_html'],
            'is_active' => (bool) ((int) ($data['is_active'] ?? 0)),
        ]);

        AdService::forgetCache();

        return redirect()->route('admin.ads.index')->with('status', 'Ad unit updated.');
    }

    public function destroyUnit(Request $request, AdUnit $unit)
    {
        $unit->delete();
        AdService::forgetCache();

        return redirect()->route('admin.ads.index')->with('status', 'Ad unit deleted.');
    }

    public function updateSlot(Request $request, AdSlot $slot)
    {
        $data = $request->validate([
            'ad_unit_id' => ['nullable', 'integer', 'exists:ad_units,id'],
            'enabled' => ['nullable', 'in:0,1'],
        ]);

        AdSlotAssignment::query()->updateOrCreate(
            ['slot_id' => $slot->id],
            [
                'ad_unit_id' => $data['ad_unit_id'] ?? null,
                'enabled' => (bool) ((int) ($data['enabled'] ?? 0)),
                'rules_json' => null,
            ]
        );

        AdService::forgetCache();

        return redirect()->route('admin.ads.index')->with('status', 'Slot updated.');
    }
}

