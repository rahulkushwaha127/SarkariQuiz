<?php

namespace App\Http\Controllers\Creator;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\Subject;

class TaxonomyController extends Controller
{
    public function subjects(Exam $exam)
    {
        $subjects = $exam->subjects()
            ->where('is_active', true)
            ->get(['id', 'name']);

        return response()->json([
            'ok' => true,
            'subjects' => $subjects,
        ]);
    }

    public function topics(Subject $subject)
    {
        $topics = $subject->topics()
            ->where('is_active', true)
            ->get(['id', 'name']);

        return response()->json([
            'ok' => true,
            'topics' => $topics,
        ]);
    }
}

