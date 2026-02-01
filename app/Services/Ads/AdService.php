<?php

namespace App\Services\Ads;

use App\Models\AdSlot;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\HtmlString;

class AdService
{
    private const CACHE_KEY = 'ads:slot_map_v1';

    /**
     * Render a slot as HTML. Returns null if slot shouldn't render.
     *
     * Options:
     * - hide_on_question_screens: bool (default true) for student question routes
     */
    public static function renderSlot(string $slotKey, array $options = []): ?HtmlString
    {
        $ads = view()->shared('ads') ?? [];
        if (!((bool)($ads['enabled'] ?? false))) {
            return null;
        }

        $hideOnQuestions = (bool)($options['hide_on_question_screens'] ?? true);
        if ($hideOnQuestions) {
            $isQuestion = request()->routeIs('student.play.question') || request()->routeIs('student.practice.question');
            if ($isQuestion) {
                return null;
            }
        }

        // Slot-level de-duplication (same include twice on same page => render once).
        $usedSlots = request()->attributes->get('_ads_used_slots', []);
        if (in_array($slotKey, $usedSlots, true)) {
            return null;
        }
        $usedSlots[] = $slotKey;
        request()->attributes->set('_ads_used_slots', $usedSlots);

        $map = self::slotMap();
        $row = $map[$slotKey] ?? null;
        if (!$row) {
            return null;
        }

        // Basic placement-level switch: banners use banner_enabled.
        if (($row['type'] ?? 'banner') === 'banner' && !((bool)($ads['banner_enabled'] ?? false))) {
            return null;
        }

        if (!((bool)($row['enabled'] ?? false))) {
            return null;
        }

        $unitKey = (string)($row['unit_key'] ?? '');
        $html = (string)($row['code_html'] ?? '');
        if ($unitKey === '' || $html === '') {
            return null;
        }

        // Unit-level de-duplication (AdSense-safe: don't render same unit twice on a page).
        $usedUnits = request()->attributes->get('_ads_used_units', []);
        if (in_array($unitKey, $usedUnits, true)) {
            return null;
        }
        $usedUnits[] = $unitKey;
        request()->attributes->set('_ads_used_units', $usedUnits);

        return new HtmlString($html);
    }

    public static function forgetCache(): void
    {
        Cache::forget(self::CACHE_KEY);
    }

    private static function slotMap(): array
    {
        try {
            if (!Schema::hasTable('ad_slots') || !Schema::hasTable('ad_slot_assignments') || !Schema::hasTable('ad_units')) {
                return [];
            }

            return Cache::remember(self::CACHE_KEY, now()->addMinutes(10), function () {
                $rows = AdSlot::query()
                    ->where('is_active', true)
                    ->with(['assignment.adUnit'])
                    ->get();

                $out = [];
                foreach ($rows as $slot) {
                    $assignment = $slot->assignment;
                    $unit = $assignment?->adUnit;
                    $out[$slot->key] = [
                        'slot_key' => $slot->key,
                        'type' => $slot->type,
                        'enabled' => (bool)($assignment?->enabled),
                        'unit_key' => $unit?->key,
                        'code_html' => $unit && $unit->is_active ? $unit->code_html : null,
                    ];
                }

                return $out;
            });
        } catch (\Throwable $e) {
            return [];
        }
    }
}

