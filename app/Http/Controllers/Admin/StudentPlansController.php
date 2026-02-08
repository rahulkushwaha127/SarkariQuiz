<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\StudentPlan;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class StudentPlansController extends Controller
{
    public function index()
    {
        $plans = StudentPlan::query()->ordered()->get();

        return view('admin.student-plans.index', compact('plans'));
    }

    public function create()
    {
        return view('admin.student-plans._form', [
            'plan' => new StudentPlan(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);

        $data['slug'] = Str::slug($data['name']);
        $base = $data['slug'];
        $i = 1;
        while (StudentPlan::where('slug', $data['slug'])->exists()) {
            $data['slug'] = $base . '-' . $i++;
        }

        StudentPlan::create($data);

        return redirect()
            ->route('admin.student-plans.index')
            ->with('status', 'Student plan created.');
    }

    public function edit(StudentPlan $student_plan)
    {
        return view('admin.student-plans._form', ['plan' => $student_plan]);
    }

    public function update(Request $request, StudentPlan $student_plan)
    {
        $student_plan->update($this->validated($request));

        return redirect()
            ->route('admin.student-plans.index')
            ->with('status', 'Student plan updated.');
    }

    public function destroy(StudentPlan $student_plan)
    {
        $usersOnPlan = \App\Models\User::where('student_plan_id', $student_plan->id)->count();
        if ($usersOnPlan > 0) {
            return back()->with('error', "Cannot delete: {$usersOnPlan} user(s) are on this plan. Reassign them first.");
        }

        $student_plan->delete();

        return redirect()
            ->route('admin.student-plans.index')
            ->with('status', 'Student plan deleted.');
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'name'        => ['required', 'string', 'max:80'],
            'description' => ['nullable', 'string', 'max:500'],
            'duration'    => ['required', 'string', 'in:weekly,monthly,yearly'],
            'price_label' => ['nullable', 'string', 'max:60'],
            'price_paise' => ['nullable', 'integer', 'min:0'],
            'sort_order'  => ['nullable', 'integer', 'min:0'],
            'is_active'   => ['nullable'],
        ]);

        $data['is_active'] = (bool) ($data['is_active'] ?? true);
        $data['sort_order'] = (int) ($data['sort_order'] ?? 0);
        $data['price_paise'] = isset($data['price_paise']) && $data['price_paise'] !== '' ? (int) $data['price_paise'] : null;

        return $data;
    }
}
