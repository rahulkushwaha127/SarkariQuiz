<?php

namespace App\Exports;

use Carbon\Carbon;
use App\Models\UserQuiz;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;

class QuizParticipantsExport implements FromCollection, WithHeadings, WithEvents
{
    protected $quiz;

    public function __construct($quiz)
    {
        $this->quiz = $quiz;
    }

    public function collection(): Collection
    {
        $totalQuestionIds = $this->quiz->questions->pluck('id')->toArray();
        $totalQuestions = count($totalQuestionIds);

        $quizUsers = UserQuiz::selectRaw('*, TIMEDIFF(completed_at, started_at) AS total_time')
            ->where('quiz_id', $this->quiz->id)
            ->orderBy('score', 'desc')
            ->orderBy('total_time', 'asc')
            ->get();

        return $quizUsers->map(function ($user, $index) use ($totalQuestionIds, $totalQuestions) {
            $result = json_decode($user->result, true);
            if (isset($result['total_question']) && isset($result['total_current_question'])) {
                $totalQuestions = $result['total_question'];
                $totalAttemptQuestion = $result['total_current_question'] + $result['total_unanswered'];
                $correctAnswers = $result['total_current_question'];
            } else {
                $totalAttemptQuestion = $user->questionAnswers->whereNotNull('completed_at')->count() ?? 0;
                $correctAnswers = $user->questionAnswers->where('is_correct', 1)->whereNotNull('completed_at')->count() ?? 0;
            }
            $start = Carbon::parse($user->started_at);
            $end = Carbon::parse($user->completed_at);
            $seconds = $start->diffInSeconds($end);

            return [
                'rank'                => $index + 1,
                'name'                => $user->name,
                'email'               => $user->email,
                'started_at'          => $start->format('d/m/Y h:i A'),
                'completed_at'        => $user->completed_at ? $end->format('d/m/Y h:i A') : '-',
                'total_time'          => $user->completed_at ? getTimeFormat($seconds) : '-',
                'score'               => ($user->score ?? 0) . '%',
                'attempted_questions' => $totalAttemptQuestion ?? 0,
                'correct_answers'     => $correctAnswers,
                'total_questions'     => $totalQuestions,
            ];
        });
    }

    public function headings(): array
    {
        return [
            [__('messages.quiz.quiz_title') . ': ' . $this->quiz->title],
            [],
            [],
            [__('messages.quiz.rank'), __('messages.common.name'), __('messages.user.email'), __('messages.participant.started_at'), __('messages.participant.completed_at'), __('messages.participant.total_time'), __('messages.participant_result.score'), __('messages.quiz.attempted_questions'), __('messages.participant.correct_answers'), __('messages.participant_result.total_questions')],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $event->sheet->mergeCells('A1:J1');

                $event->sheet->getDelegate()->getStyle('A1')->getFont()->setBold(true)->setSize(14);

                $event->sheet->getStyle('A4:J4')->applyFromArray([
                    'fill' => [
                        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                        'startColor' => [
                            'rgb' => '800080',
                        ],
                    ],
                    'font' => [
                        'color' => ['rgb' => 'FFFFFF'],
                        'bold' => true,
                    ],
                ]);
            },
        ];
    }
}
