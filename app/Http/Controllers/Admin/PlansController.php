<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class PlansController extends Controller
{
    public function index()
    {
        $plans = Plan::query()->ordered()->get();

        return view('admin.plans.index', compact('plans'));
    }

    public function create()
    {
        return view('admin.plans._form', [
            'plan' => new Plan(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);

        $data['slug'] = Str::slug($data['name']);

        // Ensure slug uniqueness
        $base = $data['slug'];
        $i = 1;
        while (Plan::where('slug', $data['slug'])->exists()) {
            $data['slug'] = $base . '-' . $i++;
        }

        if (! empty($data['is_default'])) {
            Plan::query()->update(['is_default' => false]);
        }

        Plan::create($data);

        return redirect()
            ->route('admin.plans.index')
            ->with('status', 'Plan created.');
    }

    public function edit(Plan $plan)
    {
        return view('admin.plans._form', compact('plan'));
    }

    public function update(Request $request, Plan $plan)
    {
        $data = $this->validated($request, $plan);

        if (! empty($data['is_default']) && ! $plan->is_default) {
            Plan::query()->where('id', '!=', $plan->id)->update(['is_default' => false]);
        }

        $plan->update($data);

        return redirect()
            ->route('admin.plans.index')
            ->with('status', 'Plan updated.');
    }

    public function destroy(Plan $plan)
    {
        // Don't delete if users are on this plan
        $usersOnPlan = \App\Models\User::where('plan_id', $plan->id)->count();
        if ($usersOnPlan > 0) {
            return back()->with('error', "Cannot delete: {$usersOnPlan} user(s) are on this plan. Reassign them first.");
        }

        $plan->delete();

        return redirect()
            ->route('admin.plans.index')
            ->with('status', 'Plan deleted.');
    }

    /* ------------------------------------------------------------------ */
    /*  Validation                                                         */
    /* ------------------------------------------------------------------ */

    private function validated(Request $request, ?Plan $plan = null): array
    {
        $data = $request->validate([
            'name'                         => ['required', 'string', 'max:80'],
            'description'                  => ['nullable', 'string', 'max:500'],
            'price_label'                  => ['nullable', 'string', 'max:60'],
            'max_quizzes'                  => ['nullable', 'integer', 'min:0'],
            'max_batches'                  => ['nullable', 'integer', 'min:0'],
            'max_students_per_batch'       => ['nullable', 'integer', 'min:0'],
            'max_ai_generations_per_month' => ['nullable', 'integer', 'min:0'],
            'can_access_question_bank'     => ['nullable'],
            'is_default'                   => ['nullable'],
            'is_active'                    => ['nullable'],
            'sort_order'                   => ['nullable', 'integer', 'min:0'],
        ]);

        // Normalize checkboxes and nullable integers
        $data['can_access_question_bank'] = (bool) ($data['can_access_question_bank'] ?? false);
        $data['is_default'] = (bool) ($data['is_default'] ?? false);
        $data['is_active'] = (bool) ($data['is_active'] ?? true);
        $data['sort_order'] = (int) ($data['sort_order'] ?? 0);

        // Convert empty strings to null for nullable int fields
        foreach (['max_quizzes', 'max_batches', 'max_students_per_batch', 'max_ai_generations_per_month'] as $field) {
            if (($data[$field] ?? '') === '' || ($data[$field] ?? '') === '0' && $request->input($field) === '') {
                $data[$field] = null;
            }
        }

        return $data;
    }
}
