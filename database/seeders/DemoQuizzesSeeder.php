<?php

namespace Database\Seeders;

use App\Models\Exam;
use App\Models\Quiz;
use App\Models\Subject;
use App\Models\Topic;
use App\Models\User;
use Illuminate\Database\Seeder;

class DemoQuizzesSeeder extends Seeder
{
    public function run(): void
    {
        /** @var \App\Models\User|null $creator */
        $creator = User::query()->where('email', 'creator@example.com')->first();
        if (! $creator) {
            return;
        }

        $ssc = Exam::query()->where('slug', 'ssc')->first();
        $gk = $ssc ? Subject::query()->where('exam_id', $ssc->id)->where('slug', 'general-knowledge')->first() : null;
        $ca = $gk ? Topic::query()->where('subject_id', $gk->id)->where('slug', 'current-affairs')->first() : null;

        $reasoning = $ssc ? Subject::query()->where('exam_id', $ssc->id)->where('slug', 'reasoning')->first() : null;
        $series = $reasoning ? Topic::query()->where('subject_id', $reasoning->id)->where('slug', 'series')->first() : null;

        $quizRows = [
            [
                'title' => 'Daily GK (Demo)',
                'description' => 'A quick GK warm-up for aspirants.',
                'is_public' => true,
                'mode' => 'exam',
                'status' => 'published',
                'difficulty' => 0,
                'language' => 'en',
                'exam_id' => $ssc?->id,
                'subject_id' => $gk?->id,
                'topic_id' => $ca?->id,
            ],
            [
                'title' => 'Reasoning Series (Demo)',
                'description' => 'Practice series questions.',
                'is_public' => false,
                'mode' => 'exam',
                'status' => 'draft',
                'difficulty' => 1,
                'language' => 'en',
                'exam_id' => $ssc?->id,
                'subject_id' => $reasoning?->id,
                'topic_id' => $series?->id,
            ],
        ];

        foreach ($quizRows as $row) {
            /** @var \App\Models\Quiz $quiz */
            $quiz = Quiz::query()->firstOrNew([
                'user_id' => $creator->id,
                'title' => $row['title'],
            ]);

            // DatabaseSeeder disables model events, so generate unique_code manually on create.
            if (! $quiz->exists && ! $quiz->unique_code) {
                $quiz->unique_code = Quiz::generateUniqueCode();
            }

            $quiz->fill($row);
            $quiz->user_id = $creator->id;

            $quiz->save();
        }
    }
}

