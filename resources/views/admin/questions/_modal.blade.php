@php
    $isEdit = $question->exists;
@endphp
<form method="POST" action="{{ $isEdit ? route('admin.questions.update', $question) : route('admin.questions.store') }}" class="space-y-4">
    @csrf
    @if ($isEdit)
        @method('PATCH')
    @endif
    @include('admin.questions._form', ['question' => $question])

    <div class="flex items-center justify-end gap-2">
        <button type="button" class="rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50" data-modal-close="true">
            Cancel
        </button>
        <button type="submit" class="rounded-xl bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-800">
            {{ $isEdit ? 'Save' : 'Create question' }}
        </button>
    </div>
</form>
