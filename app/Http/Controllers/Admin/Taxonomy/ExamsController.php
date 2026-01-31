<?php

namespace App\Http\Controllers\Admin\Taxonomy;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Support\Slug;
use Illuminate\Http\Request;

class ExamsController extends Controller
{
    public function index(Request $request)
    {
        $q = $request->string('q')->toString();

        $exams = Exam::query()
            ->when($q !== '', fn ($query) => $query->where('name', 'like', "%{$q}%"))
            ->orderBy('position')
            ->orderBy('name')
            ->paginate(20)
            ->withQueryString();

        return view('admin.taxonomy.exams.index', compact('exams', 'q'));
    }

    public function create()
    {
        return view('admin.taxonomy.exams._modal', ['exam' => new Exam()]);
    }

    public function edit(Exam $exam)
    {
        return view('admin.taxonomy.exams._modal', compact('exam'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'is_active' => ['nullable', 'boolean'],
            'position' => ['nullable', 'integer', 'min:0'],
        ]);

        Exam::create([
            'name' => $data['name'],
            'slug' => Slug::make($data['name']),
            'is_active' => (bool) ($data['is_active'] ?? true),
            'position' => (int) ($data['position'] ?? 0),
        ]);

        return redirect()->route('admin.taxonomy.exams.index')->with('status', 'Exam created.');
    }

    public function update(Request $request, Exam $exam)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'is_active' => ['nullable', 'boolean'],
            'position' => ['nullable', 'integer', 'min:0'],
        ]);

        $exam->update([
            'name' => $data['name'],
            'slug' => Slug::make($data['name']),
            'is_active' => (bool) ($data['is_active'] ?? true),
            'position' => (int) ($data['position'] ?? 0),
        ]);

        return redirect()->route('admin.taxonomy.exams.index')->with('status', 'Exam updated.');
    }

    public function destroy(Exam $exam)
    {
        $exam->delete();
        return back()->with('status', 'Exam deleted.');
    }
}

