<?php

namespace App\Console\Commands;

use App\Models\Question;
use App\Models\Quiz;
use App\Models\User;
use Illuminate\Console\Command;

class GenerateDemoQuizzes extends Command
{
    protected $signature = 'quizzes:generate-demo
                            {--count=30 : Number of quizzes to generate}
                            {--questions-per-quiz=10 : Questions per quiz}
                            {--refresh : Remove existing auto-generated quizzes before creating new ones}';

    protected $description = 'Generate demo quizzes from existing questions (random, language-based). Use --refresh to replace previous auto-generated quizzes only.';

    public function handle(): int
    {
        $count = max(1, min(100, (int) $this->option('count')));
        $perQuiz = max(5, min(50, (int) $this->option('questions-per-quiz')));
        $refresh = (bool) $this->option('refresh');

        $owner = User::role('super_admin')->first();
        if (! $owner) {
            $this->error('No super admin user found. Create a user with super_admin role (e.g. run seeders) before generating demo quizzes.');
            return self::FAILURE;
        }

        if ($refresh) {
            $deleted = Quiz::query()->where('is_auto_generated', true)->delete();
            $this->info("Removed {$deleted} existing auto-generated quiz(zes).");
        }

        // Questions with at least one correct answer, grouped by language then by subject (so each quiz picks from a random subject)
        $questions = Question::query()
            ->whereHas('answers', fn ($q) => $q->where('is_correct', true))
            ->select('id', 'language', 'subject_id', 'topic_id')
            ->get();

        // Group by language then by subject_id (0 = no subject) so each quiz picks from one random subject
        $byLangAndSubject = $questions->groupBy(fn ($q) => $q->language ?? 'en')
            ->map(fn ($byLang) => $byLang->groupBy(fn ($q) => (int) ($q->subject_id ?? 0))
                ->map(fn ($group) => $group->pluck('id')->all())
                ->filter(fn ($ids) => count($ids) >= $perQuiz)
                ->all())
            ->filter(fn ($subjects) => ! empty($subjects))
            ->all();

        if (empty($byLangAndSubject)) {
            $this->error('No questions with correct answers found (or not enough per subject per language). Import questions first (e.g. content:import-questions).');
            return self::FAILURE;
        }

        $languages = array_keys($byLangAndSubject);
        $this->info('Languages: ' . implode(', ', $languages));

        $created = 0;
        $target = $count;
        $attempt = 0;
        $maxAttempts = $target * 5;

        while ($created < $target && $attempt < $maxAttempts) {
            $attempt++;
            $lang = $languages[array_rand($languages)];
            $subjects = $byLangAndSubject[$lang];
            $subjectIds = array_keys($subjects);
            $subjectId = $subjectIds[array_rand($subjectIds)];
            $ids = $subjects[$subjectId];
            if (count($ids) < $perQuiz) {
                continue;
            }
            $rand = array_rand(array_flip($ids), min($perQuiz, count($ids)));
            $selected = is_array($rand) ? array_values($rand) : [$rand];
            if (count($selected) < $perQuiz) {
                shuffle($ids);
                $selected = array_slice($ids, 0, $perQuiz);
            }
            shuffle($selected);

            $meta = $this->taxonomyForQuestionIds($selected);
            $title = ($meta['label'] !== '' ? $meta['label'] . ' ' : '') . '(' . $this->languageLabel($lang) . ') #' . ($created + 1);

            $quiz = Quiz::query()->create([
                'user_id' => $owner->id,
                'exam_id' => $meta['exam_id'],
                'subject_id' => $meta['subject_id'],
                'topic_id' => $meta['topic_id'],
                'title' => $title,
                'description' => 'Auto-generated demo quiz.',
                'is_public' => true,
                'is_featured' => false,
                'difficulty' => 0,
                'language' => $lang,
                'mode' => 'exam',
                'status' => 'published',
                'is_auto_generated' => true,
            ]);

            $pivot = [];
            foreach (array_values($selected) as $pos => $qid) {
                $pivot[$qid] = ['position' => $pos];
            }
            $quiz->questions()->attach($pivot);
            $created++;
        }

        $this->info("Created {$created} demo quiz(zes).");
        return self::SUCCESS;
    }

    /**
     * @param array<int> $questionIds
     * @return array{label: string, exam_id: int|null, subject_id: int|null, topic_id: int|null}
     */
    private function taxonomyForQuestionIds(array $questionIds): array
    {
        $questions = Question::query()
            ->whereIn('id', $questionIds)
            ->with(['topic', 'subject.exam'])
            ->get();

        $topicIds = $questions->pluck('topic_id')->filter()->unique()->values()->all();
        $subjectIds = $questions->pluck('subject_id')->filter()->unique()->values()->all();

        if (count($topicIds) === 1 && count($subjectIds) <= 1) {
            $first = $questions->first();
            $topic = $first->topic;
            $subject = $first->subject;
            $parts = array_filter([$subject?->name, $topic?->name]);
            $label = implode(' - ', $parts);

            return [
                'label' => $label,
                'exam_id' => $subject?->exam_id,
                'subject_id' => $first->subject_id,
                'topic_id' => $first->topic_id,
            ];
        }

        if (count($subjectIds) === 1) {
            $first = $questions->first();
            $subject = $first->subject;

            return [
                'label' => $subject ? $subject->name : '',
                'exam_id' => $subject?->exam_id,
                'subject_id' => $first->subject_id,
                'topic_id' => null,
            ];
        }

        // Fallback: use first question that has subject or topic so title always shows one when present
        $firstWithTaxonomy = $questions->first(fn ($q) => $q->topic_id || $q->subject_id);
        if ($firstWithTaxonomy) {
            $topic = $firstWithTaxonomy->topic;
            $subject = $firstWithTaxonomy->subject;
            $parts = array_filter([$subject?->name, $topic?->name]);
            $label = implode(' - ', $parts);

            return [
                'label' => $label,
                'exam_id' => $subject?->exam_id,
                'subject_id' => $firstWithTaxonomy->subject_id,
                'topic_id' => $firstWithTaxonomy->topic_id,
            ];
        }

        return [
            'label' => '',
            'exam_id' => null,
            'subject_id' => null,
            'topic_id' => null,
        ];
    }

    private function languageLabel(string $code): string
    {
        return match (strtolower($code)) {
            'en' => 'English',
            'hi' => 'Hindi',
            default => ucfirst($code),
        };
    }
}
