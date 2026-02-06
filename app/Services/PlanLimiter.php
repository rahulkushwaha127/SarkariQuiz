<?php

namespace App\Services;

use App\Models\Batch;
use App\Models\BatchStudent;
use App\Models\Plan;
use App\Models\Quiz;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class PlanLimiter
{
    /**
     * Check whether the user is allowed to perform an action governed by plan limits.
     *
     * Supported resources: 'quizzes', 'batches', 'students_per_batch', 'ai_generations'
     *
     * @param  User   $user
     * @param  string $resource
     * @param  array  $context  Extra context, e.g. ['batch_id' => 5] for student checks
     * @return array{allowed: bool, current: int, max: int|null, message: string}
     */
    public static function check(User $user, string $resource, array $context = []): array
    {
        $plan = $user->activePlan();

        // No plan configured at all => allow everything (graceful fallback)
        if (! $plan) {
            return self::allowed(0, null);
        }

        return match ($resource) {
            'quizzes'            => self::checkQuizzes($user, $plan),
            'batches'            => self::checkBatches($user, $plan),
            'students_per_batch' => self::checkStudentsPerBatch($user, $plan, $context),
            'ai_generations'     => self::checkAiGenerations($user, $plan),
            default              => self::allowed(0, null),
        };
    }

    /* ------------------------------------------------------------------ */
    /*  Individual checks                                                  */
    /* ------------------------------------------------------------------ */

    private static function checkQuizzes(User $user, Plan $plan): array
    {
        $max = $plan->max_quizzes;
        if (is_null($max)) {
            return self::allowed(0, null);
        }

        $current = Quiz::where('user_id', $user->id)->count();

        return $current >= $max
            ? self::denied($current, $max, "You've reached the quiz limit ({$max}) on the {$plan->name} plan.")
            : self::allowed($current, $max);
    }

    private static function checkBatches(User $user, Plan $plan): array
    {
        $max = $plan->max_batches;
        if (is_null($max)) {
            return self::allowed(0, null);
        }

        $current = Batch::where('creator_user_id', $user->id)
            ->where('status', 'active')
            ->count();

        return $current >= $max
            ? self::denied($current, $max, "You've reached the batch limit ({$max}) on the {$plan->name} plan.")
            : self::allowed($current, $max);
    }

    private static function checkStudentsPerBatch(User $user, Plan $plan, array $context): array
    {
        $max = $plan->max_students_per_batch;
        if (is_null($max)) {
            return self::allowed(0, null);
        }

        $batchId = $context['batch_id'] ?? null;
        if (! $batchId) {
            return self::allowed(0, $max);
        }

        $current = BatchStudent::where('batch_id', $batchId)
            ->where('status', 'active')
            ->count();

        return $current >= $max
            ? self::denied($current, $max, "This batch has reached the student limit ({$max}) on the {$plan->name} plan.")
            : self::allowed($current, $max);
    }

    private static function checkAiGenerations(User $user, Plan $plan): array
    {
        $max = $plan->max_ai_generations_per_month;
        if (is_null($max)) {
            return self::allowed(0, null);
        }

        $current = DB::table('ai_generation_logs')
            ->where('user_id', $user->id)
            ->where('generated_at', '>=', now()->startOfMonth())
            ->count();

        return $current >= $max
            ? self::denied($current, $max, "You've used all {$max} AI generations this month on the {$plan->name} plan.")
            : self::allowed($current, $max);
    }

    /* ------------------------------------------------------------------ */
    /*  Response builders                                                  */
    /* ------------------------------------------------------------------ */

    private static function allowed(int $current, ?int $max): array
    {
        return [
            'allowed' => true,
            'current' => $current,
            'max'     => $max,
            'message' => '',
        ];
    }

    private static function denied(int $current, int $max, string $message): array
    {
        return [
            'allowed' => false,
            'current' => $current,
            'max'     => $max,
            'message' => $message,
        ];
    }
}
