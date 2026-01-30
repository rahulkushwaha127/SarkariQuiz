<?php

namespace App\Filament\User\Resources\QuizzesResource\Pages;

use App\Models\Quiz;
use App\Models\Answer;
use App\Models\Question;
use Illuminate\Support\Str;
use Filament\Actions\Action;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Http;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Session;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use fivefilters\Readability\Readability;
use fivefilters\Readability\Configuration;
use App\Filament\User\Resources\QuizzesResource;

class EditQuizzes extends EditRecord
{
    protected static string $resource = QuizzesResource::class;

    public static $tab = Quiz::TEXT_TYPE;
    public function currentActiveTab()
    {
        $pre = URL::previous();
        parse_str(parse_url($pre)['query'] ?? '', $queryParams);
        $tab = $queryParams['tab'] ?? null;
        $tabType = [
            '-subject-tab' => Quiz::SUBJECT_TYPE,
            '-text-tab' => Quiz::TEXT_TYPE,
            '-url-tab' => Quiz::URL_TYPE,
            '-upload-tab' => Quiz::UPLOAD_TYPE,
        ];

        $tabType[$tab] ?? Quiz::TEXT_TYPE;
    }

    // protected function afterValidate(): void
    // {
    //     $data = $this->form->getState();

    //     if (empty($this->data['file_upload']) && empty($data['quiz_description_text']) && empty($data['quiz_description_sub']) && empty($data['quiz_description_url'])) {
    //         Notification::make()
    //             ->danger()
    //             ->title(__('messages.quiz.quiz_description_required'))
    //             ->send();
    //         $this->halt();
    //     }
    // }


    public function fillForm(): void
    {
        $quizQuestions = Session::get('quizQuestions');
        $editedBaseData = Session::get('editedQuizDataForRegeneration');
        Session::forget('editedQuizDataForRegeneration');
        Session::forget('quizQuestions');

        $quizData = trim($quizQuestions);
        if (stripos($quizData, '```json') === 0) {
            $quizData = preg_replace('/^```json\s*|\s*```$/', '', $quizData);
            $quizData = trim($quizData);
        }

        $questionData = json_decode($quizData, true);

        if ($editedBaseData) {
            $data = $editedBaseData;

            unset($data['questions'], $data['custom_questions']);
        } else {
            $data = $this->record->attributesToArray();
            $data = $this->mutateFormDataBeforeFill($data);
        }

        $data['questions'] = [];

        if (is_array($questionData) && !empty($questionData)) {
            $questionsArray = isset($questionData['questions']) && is_array($questionData['questions'])
                ? $questionData['questions']
                : $questionData;

            foreach ($questionsArray as $question) {
                if (isset($question['question'], $question['answers']) && is_array($question['answers'])) {
                    $answersOption = array_map(function ($answer) {
                        return [
                            'title' => $answer['title'],
                            'is_correct' => $answer['is_correct'],
                            'explanation' => $answer['explanation'] ?? null
                        ];
                    }, $question['answers']);

                    $correctAnswer = array_keys(array_filter(array_column($answersOption, 'is_correct')));

                    $data['questions'][] = [
                        'title' => $question['question'],
                        'answers' => $answersOption,
                        'is_correct' => $correctAnswer,
                        'explanation' => $question['explanation'] ?? null
                    ];
                }
            }
        }

        if (empty($data['questions']) && !is_array($questionData) && isset($data['id'])) {
            $questions = Question::where('quiz_id', $data['id'])->with('answers')->get();
            foreach ($questions as $question) {
                $answersOption = $question->answers->map(function ($answer) {
                    return [
                        'title' => $answer->title,
                        'is_correct' => $answer->is_correct,
                        'explanation' => $answer->explanation ?? null
                    ];
                })->toArray();

                $correctAnswer = array_keys(array_filter(array_column($answersOption, 'is_correct')));

                $data['questions'][] = [
                    'title' => $question->title,
                    'answers' => $answersOption,
                    'explanation' => $question->explanation,
                    'is_correct' => $correctAnswer,
                    'question_id' => $question->id
                ];
            }
        }
        $this->form->fill($data);
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('back')
                ->label(__('messages.common.back'))
                ->url($this->getResource()::getUrl('index')),
        ];
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $data['type'] = getTabType();
        if ($data['type'] == Quiz::TEXT_TYPE) {
            $data['quiz_description'] = $data['quiz_description_text'];
        } elseif ($data['type'] == Quiz::SUBJECT_TYPE) {
            $data['quiz_description'] = $data['quiz_description_sub'];
        } elseif ($data['type'] == Quiz::URL_TYPE) {
            $data['quiz_description'] = $data['quiz_description_url'];
        }
        $questions = array_merge(
            $data['questions'] ?? [],
            $data['custom_questions'] ?? []
        );

        if (!empty($questions)) {
            $invalidIndex = collect($questions)->search(function ($question) use ($data) {
                $correctCount = collect($question['answers'])->where('is_correct', true)->count();

                if ($data['quiz_type'] == Quiz::SINGLE_CHOICE) {
                    return $correctCount !== 1;
                }

                return $correctCount < 1;
            });

            if ($invalidIndex !== false) {
                if ($data['quiz_type'] == Quiz::SINGLE_CHOICE) {
                    Notification::make()
                        ->danger()
                        ->title(__("messages.quiz.single_choice", ['index' => $invalidIndex + 1]))
                        ->send();
                } else {
                    Notification::make()
                        ->danger()
                        ->title(__('messages.quiz.correct_answer', ['index' => $invalidIndex + 1]))
                        ->send();
                }

                $this->halt();
            }


            $updatedQuestionIds = [];
            foreach ($questions as $index => $quizQuestion) {
                if (isset($quizQuestion['question_id'])) {
                    $question = Question::where('quiz_id', $record->id)
                        ->where('id', $quizQuestion['question_id'])
                        ->first();
                    if ($question) {
                        $question->update([
                            'title' => $quizQuestion['title'],
                            'explanation' => $quizQuestion['explanation']
                        ]);
                    } else {
                        $question = Question::create([
                            'quiz_id' => $record->id,
                            'title' => $quizQuestion['title'],
                            'explanation' => $quizQuestion['explanation']
                        ]);
                    }
                } else {
                    $question = Question::create([
                        'quiz_id' => $record->id,
                        'title' => $quizQuestion['title'],
                        'explanation' => $quizQuestion['explanation']
                    ]);
                }
                $updatedQuestionIds[] = $question->id;

                $existingAnswers = $question->answers()->get()->values();
                $updatedAnswers = [];

                if (!empty($quizQuestion['answers'])) {
                    foreach ($quizQuestion['answers'] as $index => $newAnswer) {
                        $existing = $existingAnswers->get($index);

                        if ($existing) {
                            $existing->update([
                                'title' => $newAnswer['title'],
                                'is_correct' => $newAnswer['is_correct'],
                            ]);
                            $updatedAnswers[] = $existing->id;
                        } else {
                            $created = Answer::create([
                                'question_id' => $question->id,
                                'title' => $newAnswer['title'],
                                'is_correct' => $newAnswer['is_correct'],
                            ]);
                            $updatedAnswers[] = $created->id;
                        }
                    }
                    Answer::where('question_id', $question->id)
                        ->whereNotIn('id', $updatedAnswers)
                        ->delete();
                }
            }
            Question::where('quiz_id', $record->id)
                ->whereNotIn('id', $updatedQuestionIds)
                ->delete();
        } else {
            $record->questions()->delete();
        }

        session()->forget('quizQuestions');
        unset($data['questions']);
        unset($data['custom_questions']);
        unset($data['quiz_description_text']);
        unset($data['quiz_description_sub']);
        unset($data['quiz_description_url']);
        unset($data['active_tab']);
        $data['max_questions'] = $record->questions()->count();

        $record->update($data);

        return $record;
    }


    public function getTitle(): string
    {
        return __('messages.quiz.edit_quiz');
    }

    protected function getSavedNotificationTitle(): ?string
    {
        return __('messages.quiz.quiz_updated_success');
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getFormActions(): array
    {
        return [
            parent::getFormActions()[0],
            Action::make('regenerate')
                ->label(__('messages.common.re_generate'))
                ->color('gray')
                ->action('regenerateQuestions'),

            Action::make('cancel')
                ->label(__('messages.common.cancel'))
                ->color('gray')
                ->url(QuizzesResource::getUrl('index')),

        ];
    }

    public function regenerateQuestions(): void
    {
        $currentFormState = $this->form->getState();
        $currentFormState['type'] = getTabType();
        if ($currentFormState['type'] == Quiz::TEXT_TYPE) {
            $currentFormState['quiz_description'] = $currentFormState['quiz_description_text'];
        } elseif ($currentFormState['type'] == Quiz::SUBJECT_TYPE) {
            $currentFormState['quiz_description'] = $currentFormState['quiz_description_sub'];
        } elseif ($currentFormState['type'] == Quiz::URL_TYPE) {
            $currentFormState['quiz_description'] = $currentFormState['quiz_description_url'];
        }
        Session::put('editedQuizDataForRegeneration', $currentFormState);

        $data = $this->data;
        $description = null;

        if ($data['type'] == Quiz::URL_TYPE && $data['quiz_description_url'] != null) {
            $url = $data['quiz_description_url'];

            try {
                $url = $data['quiz_description_url'];

                $ch = curl_init($url);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)');
                curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

                $response = curl_exec($ch);
                $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                curl_close($ch);

                if ($httpCode != 200) {
                    throw new \Exception('Failed to fetch the URL content. HTTP Code: ' . $httpCode);
                }

                $readability = new Readability(new Configuration);
                $readability->parse($response);
                $readability->getContent();
                $description = $readability->getExcerpt();
            } catch (\Exception $e) {
                Notification::make()
                    ->danger()
                    ->title($e->getMessage())
                    ->send();
                $this->halt();
            }
        }

        if (isset($data['quiz_document']) && !empty($data['quiz_document'])) {
            $filePath = $data['quiz_document'];
            $extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));

            if ($extension === 'pdf') {
                $description = pdfToText($filePath);
            } elseif ($extension === 'docx') {
                $description = docxToText($filePath);
            }
        }

        if (strlen($description) > 10000) {
            $description = substr($description, 0, 10000) . '...';
        }

        $quizData = [
            'Difficulty' => Quiz::DIFF_LEVEL[$data['diff_level']],
            'question_type' => Quiz::QUIZ_TYPE[$data['quiz_type']],
            'language' => getAllLanguages()[$data['language']] ?? 'English'
        ];

        $prompt = <<<PROMPT

        You are an expert in crafting engaging quizzes. Based on the quiz details provided, your task is to meticulously generate questions according to the specified question type. Your output should be exclusively in properly formatted JSON.

        **Quiz Details:**

        - **Title**: {$data['title']}
        - **Description**: {$description}
        - **Number of Questions**: {$data['max_questions']}
        - **Difficulty**: {$quizData['Difficulty']}
        - **Question Type**: {$quizData['question_type']}

        **Instructions:**

        1. **Language Requirement**: Write all quiz questions and answers in {$data['language']}.
        2. **Number of Questions**: Create exactly {$data['max_questions']} questions.
        3. **Difficulty Level**: Ensure each question adheres to the specified difficulty level: {$quizData['Difficulty']}.
        4. **Description Alignment**: Ensure that each question is relevant to and reflects key aspects of the provided description.
        5. **Question Type**: Follow the format specified below based on the question type:

        **Question Formats:**

        - **Multiple Choice**:
            - Structure your JSON with four answer options. Mark exactly two options as `is_correct: true`. Use the following format:

            [
                {
                    "question": "Your question text here",
                    "answers": [
                        {
                            "title": "Answer Option 1",
                            "is_correct": false
                        },
                        {
                            "title": "Answer Option 2",
                            "is_correct": true
                        },
                        {
                            "title": "Answer Option 3",
                            "is_correct": false
                        },
                        {
                            "title": "Answer Option 4",
                            "is_correct": true
                        }
                    ],
                    "correct_answer_key": ["Answer Option 2", "Answer Option 4"]
                }
            ]

        - **Single Choice**:
            - Use the following format with exactly two options. Mark one option as `is_correct: true` and the other as `is_correct: false`:

            [
                {
                    "question": "Your question text here",
                    "answers": [
                        {
                            "title": "Answer Option 1",
                            "is_correct": false
                        },
                        {
                            "title": "Answer Option 2",
                            "is_correct": true
                        }
                    ],
                    "correct_answer_key": "Answer Option 2"
                }
            ]

        **Guidelines:**
        - You must generate exactly **{$data['max_questions']}** questions.
        - For Multiple Choice questions, ensure that there are exactly four answer options, with two options marked as `is_correct: true`.
        - For Single Choice questions, ensure that there are exactly two answer options, with one option marked as `is_correct: true`.
        - The correct_answer_key should match the correct answer's title value(s) for Multiple Choice and Single Choice questions.
        - Ensure that each question is diverse and well-crafted, covering various relevant concepts.

        Your responses should be formatted impeccably in JSON, capturing the essence of the provided quiz details.

        PROMPT;

        if (($data['quiz_mode'] ?? null) == 1) {
            $prompt .= <<<PROMPT

        **Additional Instruction for Study Mode:**
        - Since quiz mode is set to Study, each question must also include an `"explanation"` key.
        - The explanation should clearly explain why the correct answer(s) is/are correct, in {$data['language']}.
        - Example (for Single Choice):

        {
            "question": "Sample question?",
            "answers": [
                { "title": "A", "is_correct": false },
                { "title": "B", "is_correct": true }
            ],
            "correct_answer_key": "B",
            "explanation": "Option B is correct because..."
        }
            
        PROMPT;
        }

        $aiType = getSetting()->ai_type;
        $quizText = null;

        if ($aiType === Quiz::GEMINI_AI) {
            $geminiApiKey = getSetting()->gemini_api_key;
            $model = getSetting()->gemini_ai_model;

            if (!$geminiApiKey) {
                Notification::make()->danger()->title(__('messages.quiz.set_openai_key_at_env'))->send();
                return;
            }

            try {
                // Normalize values (avoid whitespace/newlines).
                $geminiApiKey = is_string($geminiApiKey) ? trim($geminiApiKey) : $geminiApiKey;
                $model = is_string($model) ? trim($model) : $model;

                // Normalize model id to match Google API format (e.g. "gemini-2.5-flash").
                // This protects against older saved values like "Gemini 2.5 Flash (Preview)"
                // or values like "models/gemini-2.5-flash".
                if (is_string($model)) {
                    $modelLower = strtolower($model);
                    $modelLower = trim($modelLower);
                    if (str_starts_with($modelLower, 'models/')) {
                        $modelLower = substr($modelLower, strlen('models/'));
                    }
                    if (str_contains($modelLower, 'gemini') && str_contains($modelLower, '2.5') && str_contains($modelLower, 'flash')) {
                        $modelLower = 'gemini-2.5-flash';
                    } elseif (str_contains($modelLower, 'gemini') && str_contains($modelLower, '2.5') && str_contains($modelLower, 'pro')) {
                        $modelLower = 'gemini-2.5-pro';
                    }
                    $model = $modelLower;
                }

                if (! is_string($model) || $model === '') {
                    Notification::make()
                        ->danger()
                        ->title('Gemini model is not set. Please select a Gemini model in AI Integration settings.')
                        ->send();
                    return;
                }

                $geminiHttp = Http::withHeaders([
                    'Content-Type' => 'application/json',
                    // Match Postman style key usage.
                    'x-goog-api-key' => $geminiApiKey,
                ]);

                // Local-only workaround for Windows SSL/cURL CA issues.
                // Do NOT disable SSL verification in production.
                if (app()->environment('local')) {
                    $geminiHttp = $geminiHttp->withoutVerifying();
                }

                $response = $geminiHttp->post("https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent", [
                    'contents' => [['parts' => [['text' => mb_convert_encoding($prompt, 'UTF-8', 'UTF-8')]]]]
                ]);

                if ($response->failed()) {
                    Notification::make()->danger()->title($response->json()['error']['message'])->send();
                    return;
                }

                $rawText = $response->json()['candidates'][0]['content']['parts'][0]['text'] ?? null;
                $quizText = preg_replace('/^```(?:json)?|```$/im', '', $rawText);
            } catch (\Exception $exception) {
                Notification::make()->danger()->title($exception->getMessage())->send();
                return;
            }
        }

        if ($aiType === Quiz::OPEN_AI) {
            $key = getSetting()->open_api_key ?? null;
            $openAiKey = ! empty($key) ? $key : config('services.open_ai.open_api_key');
            $model = getSetting()->open_ai_model;

            // Normalize key (avoid pasting "Bearer ..." or whitespace/newlines).
            $openAiKey = is_string($openAiKey) ? trim($openAiKey) : $openAiKey;
            if (is_string($openAiKey) && str_starts_with($openAiKey, 'Bearer ')) {
                $openAiKey = trim(substr($openAiKey, strlen('Bearer ')));
            }

            if (!$openAiKey) {
                Notification::make()->danger()->title(__('messages.quiz.set_openai_key_at_env'))->send();
                return;
            }

            try {
                $openAiHttp = Http::withToken($openAiKey)
                    ->withHeaders(['Content-Type' => 'application/json'])
                    ->timeout(90);

                // Local-only workaround for Windows SSL/cURL CA issues.
                // Do NOT disable SSL verification in production.
                if (app()->environment('local')) {
                    $openAiHttp = $openAiHttp->withoutVerifying();
                }

                $quizResponse = $openAiHttp->post('https://api.openai.com/v1/chat/completions', [
                        'model' => $model,
                        'messages' => [['role' => 'user', 'content' => $prompt]]
                    ]);

                if ($quizResponse->failed()) {
                    $error = $quizResponse->json()['error']['message'] ?? 'Unknown error occurred';
                    Notification::make()->danger()->title(__('OpenAI Error'))->body($error)->send();
                    return;
                }

                $quizText = $quizResponse['choices'][0]['message']['content'] ?? null;
            } catch (\Exception $e) {
                Notification::make()->danger()->title(__('API Request Failed'))->body($e->getMessage())->send();
                Log::error('OpenAI API error: ' . $e->getMessage());
                return;
            }
        }

        if ($quizText) {
            Session::put('quizQuestions', $quizText);
            $this->fillForm();
        } else {
            Notification::make()
                ->danger()
                ->title('Quiz generation failed.')
                ->send();
        }
    }
}
