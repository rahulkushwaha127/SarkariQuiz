<?php

namespace App\Http\Controllers\Admin\Taxonomy;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\Subject;
use App\Support\Slug;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SubjectsController extends Controller
{
    public function index(Request $request)
    {
        $q = $request->string('q')->toString();
        $examId = $request->integer('exam_id');

        $subjects = Subject::query()
            ->with('exams')
            ->when($examId, fn ($query) => $query->whereHas('exams', fn ($e) => $e->where('exams.id', $examId)))
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
        $subject->load('exams');
        $exams = Exam::query()->orderBy('position')->orderBy('name')->get();
        return view('admin.taxonomy.subjects._modal', compact('subject', 'exams'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'exam_ids' => ['nullable', 'array'],
            'exam_ids.*' => ['integer', 'exists:exams,id'],
            'name' => ['required', 'string', 'max:120'],
            'is_active' => ['nullable', 'boolean'],
            'position' => ['nullable', 'integer', 'min:0'],
        ]);

        $subject = Subject::create([
            'name' => $data['name'],
            'slug' => Slug::make($data['name']),
            'is_active' => (bool) ($data['is_active'] ?? true),
            'position' => (int) ($data['position'] ?? 0),
        ]);

        // Link to exams via pivot
        $examIds = array_filter(array_map('intval', $data['exam_ids'] ?? []));
        foreach ($examIds as $examId) {
            DB::table('exam_subject')->insertOrIgnore([
                'exam_id' => $examId,
                'subject_id' => $subject->id,
                'position' => (int) ($data['position'] ?? 0),
            ]);
        }

        return redirect()->route('admin.taxonomy.subjects.index')->with('status', 'Subject created.');
    }

    public function update(Request $request, Subject $subject)
    {
        $data = $request->validate([
            'exam_ids' => ['nullable', 'array'],
            'exam_ids.*' => ['integer', 'exists:exams,id'],
            'name' => ['required', 'string', 'max:120'],
            'is_active' => ['nullable', 'boolean'],
            'position' => ['nullable', 'integer', 'min:0'],
        ]);

        $subject->update([
            'name' => $data['name'],
            'slug' => Slug::make($data['name']),
            'is_active' => (bool) ($data['is_active'] ?? true),
            'position' => (int) ($data['position'] ?? 0),
        ]);

        // Sync exams via pivot
        $examIds = array_filter(array_map('intval', $data['exam_ids'] ?? []));
        $subject->exams()->sync($examIds);

        return redirect()->route('admin.taxonomy.subjects.index')->with('status', 'Subject updated.');
    }

    public function destroy(Subject $subject)
    {
        $subject->delete();
        return back()->with('status', 'Subject deleted.');
    }
}
