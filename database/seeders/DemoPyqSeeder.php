<?php

namespace Database\Seeders;

use App\Models\Exam;
use App\Models\PyqAnswer;
use App\Models\PyqQuestion;
use App\Models\Subject;
use App\Models\Topic;
use Illuminate\Database\Seeder;

class DemoPyqSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $ssc = Exam::query()->where('slug', 'ssc')->first();
        if (! $ssc) return;

        $gk = Subject::query()->where('exam_id', $ssc->id)->where('slug', 'general-knowledge')->first();
        $ca = $gk ? Topic::query()->where('subject_id', $gk->id)->where('slug', 'current-affairs')->first() : null;

        $rows = [
            [
                'year' => 2023,
                'paper' => 'SSC (Demo) 2023',
                'prompt' => 'Who appoints the Chief Election Commissioner of India?',
                'answers' => ['Prime Minister', 'Parliament', 'President of India', 'Supreme Court'],
                'correct' => 2,
                'explanation' => 'The President appoints the Chief Election Commissioner.',
            ],
            [
                'year' => 2022,
                'paper' => 'SSC (Demo) 2022',
                'prompt' => 'The capital of Australia is:',
                'answers' => ['Sydney', 'Melbourne', 'Canberra', 'Perth'],
                'correct' => 2,
                'explanation' => 'Canberra is the capital of Australia.',
            ],
            [
                'year' => 2021,
                'paper' => 'SSC (Demo) 2021',
                'prompt' => 'Which is the largest ocean in the world?',
                'answers' => ['Atlantic', 'Indian', 'Arctic', 'Pacific'],
                'correct' => 3,
                'explanation' => 'The Pacific Ocean is the largest.',
            ],
        ];

        foreach ($rows as $idx => $r) {
            $q = PyqQuestion::query()->firstOrCreate(
                [
                    'exam_id' => $ssc->id,
                    'subject_id' => $gk?->id,
                    'topic_id' => $ca?->id,
                    'year' => (int) $r['year'],
                    'prompt' => $r['prompt'],
                ],
                [
                    'paper' => $r['paper'],
                    'explanation' => $r['explanation'] ?? null,
                    'position' => $idx + 1,
                ]
            );

            $existing = (int) PyqAnswer::query()->where('pyq_question_id', $q->id)->count();
            if ($existing > 0) continue;

            foreach ($r['answers'] as $pos => $title) {
                PyqAnswer::query()->create([
                    'pyq_question_id' => $q->id,
                    'title' => $title,
                    'is_correct' => $pos === (int) $r['correct'],
                    'position' => $pos + 1,
                ]);
            }
        }
    }
}
