<?php

namespace App\Services\Ai;

class QuizJsonParser
{
    /**
     * @return array<int, array{question:string,answers:array<int,array{title:string,is_correct?:bool}>,correct_answer_key:string,explanation?:string}>
     */
    public static function parseQuestions(string $raw): array
    {
        $text = trim($raw);
        $text = preg_replace('/^```(?:json)?\\s*/i', '', $text) ?? $text;
        $text = preg_replace('/\\s*```$/', '', $text) ?? $text;
        $text = trim($text);

        $decoded = json_decode($text, true);
        if (! is_array($decoded)) {
            throw new \RuntimeException('AI output was not valid JSON.');
        }

        return $decoded;
    }

    public static function validateSingleCorrect(array $questions, int $expectedCount): void
    {
        if (count($questions) !== $expectedCount) {
            throw new \RuntimeException("AI output returned ".count($questions)." questions, expected {$expectedCount}.");
        }

        foreach ($questions as $i => $q) {
            if (! is_array($q) || empty($q['question']) || ! is_array($q['answers'])) {
                throw new \RuntimeException("Invalid question schema at index ".($i + 1).".");
            }

            if (count($q['answers']) !== 4) {
                throw new \RuntimeException("Question ".($i + 1)." must have exactly 4 answers.");
            }

            if (! array_key_exists('correct_answer_key', $q) || ! is_string($q['correct_answer_key'])) {
                throw new \RuntimeException("Question ".($i + 1)." missing correct_answer_key.");
            }

            $titles = array_map(fn ($a) => (string) ($a['title'] ?? ''), $q['answers']);
            if (! in_array($q['correct_answer_key'], $titles, true)) {
                throw new \RuntimeException("Question ".($i + 1)." correct_answer_key does not match any answer title.");
            }
        }
    }
}

