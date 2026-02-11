<?php

namespace App\Http\Controllers\Admin\Taxonomy;

use App\Http\Controllers\Controller;
use App\Models\Subject;
use App\Models\Topic;
use App\Support\Slug;
use Illuminate\Http\Request;

class TopicsController extends Controller
{
    public function index(Request $request)
    {
        $q = $request->string('q')->toString();
        $subjectId = $request->integer('subject_id');

        $topics = Topic::query()
            ->with('subject.exam')
            ->when($subjectId, fn ($query) => $query->where('subject_id', $subjectId))
            ->when($q !== '', fn ($query) => $query->where('name', 'like', "%{$q}%"))
            ->orderBy('position')
            ->orderBy('name')
            ->paginate(20)
            ->withQueryString();

        $subjects = Subject::query()
            ->with('exam')
            ->orderBy('position')
            ->orderBy('name')
            ->get();

        return view('admin.taxonomy.topics.index', compact('topics', 'q', 'subjectId', 'subjects'));
    }

    public function create()
    {
        $subjects = Subject::query()->with('exam')->orderBy('position')->orderBy('name')->get();
        return view('admin.taxonomy.topics._modal', ['topic' => new Topic(), 'subjects' => $subjects]);
    }

    public function edit(Topic $topic)
    {
        $subjects = Subject::query()->with('exam')->orderBy('position')->orderBy('name')->get();
        return view('admin.taxonomy.topics._modal', compact('topic', 'subjects'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'subject_id' => ['required', 'integer', 'exists:subjects,id'],
            'name' => ['required', 'string', 'max:120'],
            'is_active' => ['nullable', 'boolean'],
            'position' => ['nullable', 'integer', 'min:0'],
        ]);

        Topic::create([
            'subject_id' => (int) $data['subject_id'],
            'name' => $data['name'],
            'slug' => Slug::make($data['name']),
            'is_active' => (bool) ($data['is_active'] ?? true),
            'position' => (int) ($data['position'] ?? 0),
        ]);

        return redirect()->route('admin.taxonomy.topics.index')->with('status', 'Topic created.');
    }

    public function update(Request $request, Topic $topic)
    {
        $data = $request->validate([
            'subject_id' => ['required', 'integer', 'exists:subjects,id'],
            'name' => ['required', 'string', 'max:120'],
            'is_active' => ['nullable', 'boolean'],
            'position' => ['nullable', 'integer', 'min:0'],
        ]);

        $topic->update([
            'subject_id' => (int) $data['subject_id'],
            'name' => $data['name'],
            'slug' => Slug::make($data['name']),
            'is_active' => (bool) ($data['is_active'] ?? true),
            'position' => (int) ($data['position'] ?? 0),
        ]);

        return redirect()->route('admin.taxonomy.topics.index')->with('status', 'Topic updated.');
    }

    public function destroy(Topic $topic)
    {
        $topic->delete();
        return back()->with('status', 'Topic deleted.');
    }

    public function toggleActive(Topic $topic)
    {
        $topic->update(['is_active' => ! $topic->is_active]);
        if (request()->wantsJson() || request()->ajax()) {
            return response()->json(['active' => (bool) $topic->is_active]);
        }
        return back()->with('status', $topic->is_active ? 'Topic visible.' : 'Topic hidden.');
    }
}

