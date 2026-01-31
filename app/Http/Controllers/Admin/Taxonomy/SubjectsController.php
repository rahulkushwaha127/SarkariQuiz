<?php

namespace App\Http\Controllers\Admin\Taxonomy;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\Subject;
use App\Support\Slug;
use Illuminate\Http\Request;

class SubjectsController extends Controller
{
    public function index(Request $request)
    {
        $q = $request->string('q')->toString();
        $examId = $request->integer('exam_id');

        $subjects = Subject::query()
            ->with('exam')
            ->when($examId, fn ($query) => $query->where('exam_id', $examId))
            ->when($q !== '', fn ($query) => $query->where('name', 'like', "%{$q}%"))
            ->orderBy('position')
            ->orderBy('name')
            ->paginate(20)
            ->withQueryString();

        $exams = Exam::query()->orderBy('position')->orderBy('name')->get();

        return view('admin.taxonomy.subjects.index', compact('subjects', 'q', 'examId', 'exams'));
    }

    public function create()
    {
        $exams = Exam::query()->orderBy('position')->orderBy('name')->get();
        return view('admin.taxonomy.subjects._modal', ['subject' => new Subject(), 'exams' => $exams]);
    }

    public function edit(Subject $subject)
    {
        $exams = Exam::query()->orderBy('position')->orderBy('name')->get();
        return view('admin.taxonomy.subjects._modal', compact('subject', 'exams'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'exam_id' => ['required', 'integer', 'exists:exams,id'],
            'name' => ['required', 'string', 'max:120'],
            'is_active' => ['nullable', 'boolean'],
            'position' => ['nullable', 'integer', 'min:0'],
        ]);

        Subject::create([
            'exam_id' => (int) $data['exam_id'],
            'name' => $data['name'],
            'slug' => Slug::make($data['name']),
            'is_active' => (bool) ($data['is_active'] ?? true),
            'position' => (int) ($data['position'] ?? 0),
        ]);

        return redirect()->route('admin.taxonomy.subjects.index')->with('status', 'Subject created.');
    }

    public function update(Request $request, Subject $subject)
    {
        $data = $request->validate([
            'exam_id' => ['required', 'integer', 'exists:exams,id'],
            'name' => ['required', 'string', 'max:120'],
            'is_active' => ['nullable', 'boolean'],
            'position' => ['nullable', 'integer', 'min:0'],
        ]);

        $subject->update([
            'exam_id' => (int) $data['exam_id'],
            'name' => $data['name'],
            'slug' => Slug::make($data['name']),
            'is_active' => (bool) ($data['is_active'] ?? true),
            'position' => (int) ($data['position'] ?? 0),
        ]);

        return redirect()->route('admin.taxonomy.subjects.index')->with('status', 'Subject updated.');
    }

    public function destroy(Subject $subject)
    {
        $subject->delete();
        return back()->with('status', 'Subject deleted.');
    }
}

