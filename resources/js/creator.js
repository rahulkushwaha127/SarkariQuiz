function $(selector, root = document) {
    return root.querySelector(selector);
}

async function fetchJson(url) {
    const res = await fetch(url, {
        headers: { 'X-Requested-With': 'XMLHttpRequest' },
        credentials: 'same-origin',
        cache: 'no-store',
    });
    if (!res.ok) throw new Error('Request failed');
    return await res.json();
}

function setOptions(selectEl, items, placeholder) {
    if (!selectEl) return;
    const selected = selectEl.getAttribute('data-selected') || '';

    selectEl.innerHTML = '';
    const ph = document.createElement('option');
    ph.value = '';
    ph.textContent = placeholder;
    selectEl.appendChild(ph);

    for (const item of items) {
        const opt = document.createElement('option');
        opt.value = String(item.id);
        opt.textContent = item.name;
        if (selected && String(item.id) === selected) opt.selected = true;
        selectEl.appendChild(opt);
    }
}

document.addEventListener('DOMContentLoaded', () => {
    const examSelect = $('#exam_id');
    const subjectSelect = $('#subject_id');
    const topicSelect = $('#topic_id');

    if (!examSelect || !subjectSelect || !topicSelect) return;

    const subjectsUrlTemplate = examSelect.getAttribute('data-subjects-url-template');
    const topicsUrlTemplate = subjectSelect.getAttribute('data-topics-url-template');

    async function loadSubjects() {
        const examId = examSelect.value;
        const keepSelected = subjectSelect.getAttribute('data-selected') || '';

        if (!keepSelected) subjectSelect.setAttribute('data-selected', '');
        topicSelect.setAttribute('data-selected', '');

        setOptions(subjectSelect, [], 'Select subject');
        setOptions(topicSelect, [], 'Select topic');

        if (!examId) return;
        if (!subjectsUrlTemplate) return;

        const url = subjectsUrlTemplate.replace('__EXAM__', encodeURIComponent(examId));
        const json = await fetchJson(url);
        setOptions(subjectSelect, json.subjects || [], 'Select subject');
    }

    async function loadTopics() {
        const subjectId = subjectSelect.value;
        const keepSelected = topicSelect.getAttribute('data-selected') || '';
        if (!keepSelected) topicSelect.setAttribute('data-selected', '');
        setOptions(topicSelect, [], 'Select topic');

        if (!subjectId) return;
        if (!topicsUrlTemplate) return;

        const url = topicsUrlTemplate.replace('__SUBJECT__', encodeURIComponent(subjectId));
        const json = await fetchJson(url);
        setOptions(topicSelect, json.topics || [], 'Select topic');
    }

    examSelect.addEventListener('change', () => {
        loadSubjects().catch(() => {});
    });

    subjectSelect.addEventListener('change', () => {
        loadTopics().catch(() => {});
    });

    // Initial load for edit page:
    // If exam selected but subjects list is empty, fetch subjects; same for topics.
    if (examSelect.value && subjectSelect.options.length <= 1) {
        loadSubjects()
            .then(() => {
                if (subjectSelect.value) return loadTopics();
                return null;
            })
            .catch(() => {});
    } else if (subjectSelect.value && topicSelect.options.length <= 1) {
        loadTopics().catch(() => {});
    }
});

