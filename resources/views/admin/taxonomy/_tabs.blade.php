<div class="flex flex-wrap gap-2">
    <a href="{{ route('admin.taxonomy.exams.index') }}"
       class="rounded-xl px-3 py-2 text-sm font-semibold {{ request()->routeIs('admin.taxonomy.exams.*') ? 'bg-slate-900 text-white' : 'border border-slate-200 bg-white text-slate-700 hover:bg-slate-50' }}">
        Exams
    </a>
    <a href="{{ route('admin.taxonomy.subjects.index') }}"
       class="rounded-xl px-3 py-2 text-sm font-semibold {{ request()->routeIs('admin.taxonomy.subjects.*') ? 'bg-slate-900 text-white' : 'border border-slate-200 bg-white text-slate-700 hover:bg-slate-50' }}">
        Subjects
    </a>
    <a href="{{ route('admin.taxonomy.topics.index') }}"
       class="rounded-xl px-3 py-2 text-sm font-semibold {{ request()->routeIs('admin.taxonomy.topics.*') ? 'bg-slate-900 text-white' : 'border border-slate-200 bg-white text-slate-700 hover:bg-slate-50' }}">
        Topics
    </a>
    <a href="{{ route('admin.taxonomy.subtopics.index') }}"
       class="rounded-xl px-3 py-2 text-sm font-semibold {{ request()->routeIs('admin.taxonomy.subtopics.*') ? 'bg-slate-900 text-white' : 'border border-slate-200 bg-white text-slate-700 hover:bg-slate-50' }}">
        Subtopics
    </a>
</div>

