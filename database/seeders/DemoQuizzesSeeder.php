<?php

namespace Database\Seeders;

use App\Models\Answer;
use App\Models\Exam;
use App\Models\Question;
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

            // Ensure demo quiz has questions so play doesn't 422.
            if ($quiz->title === 'Daily GK (Demo)') {
                $existingCount = (int) Question::query()->where('quiz_id', $quiz->id)->count();
                if ($existingCount <= 0) {
                    $demo = [
                        [
                            'prompt' => 'Who is known as the “Father of the Indian Constitution”?',
                            'answers' => ['Mahatma Gandhi', 'B. R. Ambedkar', 'Jawaharlal Nehru', 'Sardar Patel'],
                            'correct' => 1,
                            'explanation' => 'Dr. B. R. Ambedkar chaired the Drafting Committee of the Constitution.',
                        ],
                        [
                            'prompt' => 'Which is the largest planet in our Solar System?',
                            'answers' => ['Earth', 'Mars', 'Jupiter', 'Venus'],
                            'correct' => 2,
                            'explanation' => 'Jupiter is the largest planet in the Solar System.',
                        ],
                        [
                            'prompt' => 'The currency of Japan is:',
                            'answers' => ['Yuan', 'Yen', 'Won', 'Dollar'],
                            'correct' => 1,
                            'explanation' => 'Japan uses the Japanese Yen (JPY).',
                        ],
                        [
                            'prompt' => 'The “Right to Information” in India is provided under which Act?',
                            'answers' => ['RTI Act 2005', 'Consumer Protection Act 1986', 'IT Act 2000', 'RTE Act 2009'],
                            'correct' => 0,
                            'explanation' => 'The Right to Information Act was enacted in 2005.',
                        ],
                        [
                            'prompt' => 'Which gas is most abundant in the Earth’s atmosphere?',
                            'answers' => ['Oxygen', 'Nitrogen', 'Carbon Dioxide', 'Hydrogen'],
                            'correct' => 1,
                            'explanation' => 'Nitrogen is ~78% of Earth’s atmosphere.',
                        ],
                    ];

                    foreach ($demo as $i => $q) {
                        $question = Question::query()->create([
                            'quiz_id' => $quiz->id,
                            'prompt' => $q['prompt'],
                            'explanation' => $q['explanation'] ?? null,
                            'position' => $i + 1,
                        ]);

                        foreach ($q['answers'] as $pos => $title) {
                            Answer::query()->create([
                                'question_id' => $question->id,
                                'title' => $title,
                                'is_correct' => $pos === (int) $q['correct'],
                                'position' => $pos + 1,
                            ]);
                        }
                    }
                }
            }
        }
    }
}

