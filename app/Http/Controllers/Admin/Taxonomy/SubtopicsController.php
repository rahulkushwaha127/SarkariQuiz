<?php

namespace App\Http\Controllers\Admin\Taxonomy;

use App\Http\Controllers\Controller;
use App\Models\Subject;
use App\Models\Subtopic;
use App\Models\Topic;
use App\Support\Slug;
use Illuminate\Http\Request;

class SubtopicsController extends Controller
{
    public function index(Request $request)
    {
        $q = $request->string('q')->toString();
        $topicId = $request->integer('topic_id');
        $subjectId = $request->integer('subject_id');

        $subtopics = Subtopic::query()
            ->with('topic.subject')
            ->when($topicId, fn ($query) => $query->where('topic_id', $topicId))
            ->when($subjectId, fn ($query) => $query->whereHas('topic', fn ($t) => $t->where('subject_id', $subjectId)))
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

        $topics = Topic::query()
            ->with('subject')
            ->when($subjectId, fn ($query) => $query->where('subject_id', $subjectId))
            ->orderBy('position')
            ->orderBy('name')
            ->get();

        return view('admin.taxonomy.subtopics.index', compact('subtopics', 'q', 'topicId', 'subjectId', 'subjects', 'topics'));
    }

    public function create()
    {
        $topics = Topic::query()->with('subject')->orderBy('position')->orderBy('name')->get();
        return view('admin.taxonomy.subtopics._modal', ['subtopic' => new Subtopic(), 'topics' => $topics]);
    }

    public function edit(Subtopic $subtopic)
    {
        $topics = Topic::query()->with('subject')->orderBy('position')->orderBy('name')->get();
        return view('admin.taxonomy.subtopics._modal', compact('subtopic', 'topics'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'topic_id' => ['required', 'integer', 'exists:topics,id'],
            'name' => ['required', 'string', 'max:120'],
            'is_active' => ['nullable', 'boolean'],
            'position' => ['nullable', 'integer', 'min:0'],
        ]);

        Subtopic::create([
            'topic_id' => (int) $data['topic_id'],
            'name' => $data['name'],
            'slug' => Slug::make($data['name']),
            'is_active' => (bool) ($data['is_active'] ?? true),
            'position' => (int) ($data['position'] ?? 0),
        ]);

        return redirect()->route('admin.taxonomy.subtopics.index')->with('status', 'Subtopic created.');
    }

    public function update(Request $request, Subtopic $subtopic)
    {
        $data = $request->validate([
            'topic_id' => ['required', 'integer', 'exists:topics,id'],
            'name' => ['required', 'string', 'max:120'],
            'is_active' => ['nullable', 'boolean'],
            'position' => ['nullable', 'integer', 'min:0'],
        ]);

        $subtopic->update([
            'topic_id' => (int) $data['topic_id'],
            'name' => $data['name'],
            'slug' => Slug::make($data['name']),
            'is_active' => (bool) ($data['is_active'] ?? true),
            'position' => (int) ($data['position'] ?? 0),
        ]);

        return redirect()->route('admin.taxonomy.subtopics.index')->with('status', 'Subtopic updated.');
    }

    public function destroy(Subtopic $subtopic)
    {
        $subtopic->delete();
        return back()->with('status', 'Subtopic deleted.');
    }

    public function toggleActive(Subtopic $subtopic)
    {
        $subtopic->update(['is_active' => ! $subtopic->is_active]);
        if (request()->wantsJson() || request()->ajax()) {
            return response()->json(['active' => (bool) $subtopic->is_active]);
        }
        return back()->with('status', $subtopic->is_active ? 'Subtopic visible.' : 'Subtopic hidden.');
    }
}
