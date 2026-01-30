<?php

namespace App\Models;

use Carbon\Carbon;
use Filament\Forms\Get;
use Spatie\MediaLibrary\HasMedia;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Radio;
use Illuminate\Support\Facades\Date;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Forms\Components\Textarea;
use Illuminate\Database\Eloquent\Model;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\CheckboxList;
use Spatie\MediaLibrary\InteractsWithMedia;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Placeholder;
use Illuminate\Support\HtmlString;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\ToggleButtons;

class Quiz extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    protected $table = 'quizzes';

    public const QUIZ_PATH = 'quiz_document';

    protected $fillable = [
        'title',
        'quiz_description',
        'user_id',
        'status',
        'type',
        'category_id',
        'diff_level',
        'quiz_type',
        'max_questions',
        'unique_code',
        'view_count',
        'time_configuration',
        'time',
        'time_type',
        'quiz_expiry_date',
        'is_show_home',
        'quiz_mode',
    ];

    protected $casts = [
        'title' => 'string',
        'quiz_description' => 'string',
        'user_id' => 'integer',
        'status' => 'boolean',
        'type' => 'integer',
        'diff_level' => 'integer',
        'quiz_type' => 'integer',
        'max_questions' => 'integer',
        'unique_code' => 'string',
        'view_count' => 'integer',
    ];

    const TEXT_TYPE = 1;

    const SUBJECT_TYPE = 2;

    const URL_TYPE = 3;

    const UPLOAD_TYPE = 4;

    const TIME_OVER_QUESTION = 1;

    const TIME_OVER_QUIZ = 2;

    const STUDY = 1;

    const EXAM = 2;

    const QUIZ_INPUT_TYPE = [
        self::TEXT_TYPE => 'Text',
        self::SUBJECT_TYPE => 'Subject',
        self::URL_TYPE => 'URL',
        self::UPLOAD_TYPE => 'Upload File',
    ];

    const OPEN_AI = 1;

    const GEMINI_AI = 2;

    const AI_TYPES = [
        self::OPEN_AI => 'Open AI',
        self::GEMINI_AI => 'Gemini AI',
    ];

    protected $appends = [
        'quiz_document',
        'question_count',
    ];

    public function getQuizDocumentAttribute()
    {
        return $this->getFirstMediaUrl(self::QUIZ_PATH);
    }

    const MULTIPLE_CHOICE = 0;
    const SINGLE_CHOICE = 1;
    const QUIZ_TYPE = [
        self::MULTIPLE_CHOICE => 'Multiple Choices',
        self::SINGLE_CHOICE => 'Single Choice',
    ];

    public static function getQuizTypeOptions()
    {
        return [
            0 => __('messages.home.multiple_choice'),
            1 => __('messages.home.single_choice'),
        ];
    }

    const DIFF_LEVEL = [
        0 => 'Basic',
        1 => 'Intermediate',
        2 => 'Advanced',
    ];

    public static function getDiffLevelOptions()
    {
        return [
            0 => __('messages.quiz.basic'),
            1 => __('messages.quiz.intermediate'),
            2 => __('messages.quiz.advanced'),
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function questions()
    {
        return $this->hasMany(Question::class);
    }

    protected function getQuestionCountAttribute()
    {
        return $this->questions()->count();
    }

    public function quizUser()
    {
        return $this->hasMany(UserQuiz::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public static function  getForm(): array
    {
        return [
            Section::make()
                ->schema([
                    Grid::make(2)
                        ->schema([
                            Grid::make(1)
                                ->schema([
                                    Group::make([
                                        TextInput::make('title')
                                            ->label(__('messages.quiz.title') . ':')
                                            ->placeholder(__('messages.quiz.quiz_title'))
                                            ->validationAttribute(__('messages.quiz.title'))
                                            ->required(),
                                        Select::make('category_id')
                                            ->label(__('messages.quiz.select_category') . ':')
                                            ->placeholder(__('messages.quiz.select_category'))
                                            ->validationAttribute(__('messages.quiz.category'))
                                            ->options(function () {
                                                return Category::all()->pluck('name', 'id');
                                            })
                                            ->searchable()
                                            ->required()
                                            ->preload()
                                            ->native(false)
                                    ]),
                                    Section::make()
                                        ->schema([
                                            Select::make('quiz_type')
                                                ->label(__('messages.quiz.question_type') . ':')
                                                ->options(Quiz::getQuizTypeOptions())
                                                ->default(0)
                                                ->searchable()
                                                ->required()
                                                ->preload()
                                                ->live()
                                                ->native(false)
                                                ->placeholder(__('messages.quiz.select_question'))
                                                ->validationAttribute(__('messages.quiz.question_type')),
                                            Select::make('diff_level')
                                                ->label(__('messages.quiz.difficulty') . ':')
                                                ->options(Quiz::getDiffLevelOptions())
                                                ->default(0)
                                                ->required()
                                                ->searchable()
                                                ->preload()
                                                ->native(false)
                                                ->placeholder(__('messages.quiz.select_difficulty'))
                                                ->validationAttribute(__('messages.quiz.difficulty')),
                                            TextInput::make('max_questions')
                                                ->numeric()
                                                ->rules(['integer', 'max:25'])
                                                ->integer()
                                                ->required()
                                                ->minValue(1)
                                                ->maxValue(25)
                                                ->label(__('messages.quiz.num_of_questions') . ':')
                                                ->hintIcon('heroicon-m-question-mark-circle', tooltip: __('messages.quiz.max_no_of_quiz'))
                                                ->placeholder(__('messages.quiz.number_of_questions'))
                                                ->validationAttribute(__('messages.quiz.num_of_questions'))
                                                ->hidden(function ($get) {
                                                    return empty($get('add_manually_questions')) ? false : true;
                                                }),
                                            Select::make('language')
                                                ->label(__('messages.home.language') . ':')
                                                ->options(getAllLanguages())
                                                ->preload()
                                                ->searchable()
                                                ->native(false)
                                                ->default('en')
                                                ->validationAttribute(__('messages.home.language'))
                                        ])
                                        ->columns(2),
                                ])
                                ->columnSpan(1),

                            Grid::make(1)
                                ->schema([
                                    Tabs::make('Tabs')
                                        ->tabs([
                                            Tab::make('Text')
                                                ->label(__('messages.quiz.text'))
                                                ->schema([
                                                    Textarea::make('quiz_description_text')
                                                        ->label(__('messages.quiz.description') . ':')
                                                        ->placeholder(__('messages.quiz.quiz_description'))
                                                        ->formatStateUsing(function ($get, $operation) {
                                                            if ($operation == 'edit' && $get('type') == 1) {
                                                                return $get('quiz_description');
                                                            }
                                                        })
                                                        ->required(function ($get) {
                                                            return getTabType() == 1 || $get('type') == 1;
                                                        })
                                                        ->live()
                                                        ->validationAttribute(__('messages.quiz.description'))
                                                        ->rows(5)
                                                        ->cols(10),
                                                ]),
                                            Tab::make('Subject')
                                                ->label(__('messages.quiz.subject'))
                                                ->hidden(function ($get) {
                                                    return empty($get('add_manually_questions')) ? false : true;
                                                })
                                                ->schema([
                                                    TextInput::make('quiz_description_sub')
                                                        ->label(__('messages.quiz.subject') . ':')
                                                        ->placeholder(__('messages.quiz.e_g_biology'))
                                                        ->formatStateUsing(function ($get, $operation) {
                                                            if ($operation == 'edit' && $get('type') == 2) {
                                                                return $get('quiz_description');
                                                            }
                                                        })
                                                        ->required(function ($get) {
                                                            return getTabType() == 2 || $get('type') == 2;
                                                        })
                                                        ->live()
                                                        ->validationAttribute(__('messages.quiz.subject'))
                                                        ->maxLength(250)
                                                        ->helperText(__('messages.quiz.enter_a_subject_to_generate_question_about'))
                                                        ->autocomplete('off'),
                                                ]),
                                            Tab::make('URL')
                                                ->hidden(function ($get) {
                                                    return empty($get('add_manually_questions')) ? false : true;
                                                })
                                                ->label(__('messages.quiz.url'))
                                                ->schema([
                                                    TextInput::make('quiz_description_url')
                                                        ->label(__('messages.quiz.url') . ':')
                                                        ->formatStateUsing(function ($get, $operation) {
                                                            if ($operation == 'edit' && $get('type') == 3) {
                                                                return $get('quiz_description');
                                                            }
                                                        })
                                                        ->required(function ($get) {
                                                            return getTabType() == 3 || $get('type') == 3;
                                                        })
                                                        ->live()
                                                        ->validationAttribute(__('messages.quiz.url'))
                                                        ->url()
                                                        ->placeholder(__('messages.quiz.please_enter_url')),
                                                ]),
                                            Tab::make('Upload')
                                                ->hidden(function ($get) {
                                                    return empty($get('add_manually_questions')) ? false : true;
                                                })
                                                ->label(__('messages.quiz.upload'))
                                                ->schema([
                                                    SpatieMediaLibraryFileUpload::make('file_upload')
                                                        ->label(__('messages.quiz.document') . ':')
                                                        ->hintIcon('heroicon-m-question-mark-circle', tooltip: __('messages.quiz.file_upload_hint'))
                                                        ->validationAttribute(__('messages.quiz.document'))
                                                        ->disk(config('app.media_disk'))
                                                        ->required(function ($get) {
                                                            return getTabType() == 4 || $get('type') == 4;
                                                        })
                                                        ->live()
                                                        ->collection(Quiz::QUIZ_PATH)
                                                        ->acceptedFileTypes(['application/pdf', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document']),
                                                ]),
                                        ])
                                        ->activeTab(function ($get) {
                                            return $get('type') ?? 1;
                                        })
                                        ->extraAttributes([
                                            'wire:click' => 'currentActiveTab',
                                        ])
                                        ->persistTabInQueryString(),
                                    Section::make()
                                        ->schema([
                                            ToggleButtons::make('quiz_mode')
                                                ->options([
                                                    1 => __('messages.quiz.study'),
                                                    2 => __('messages.quiz.exam'),
                                                ])
                                                ->formatStateUsing(function ($state) {
                                                    return $state ? $state : 2;
                                                })
                                                ->label(__('messages.quiz.quiz_mode') . ':')
                                                ->required()
                                                ->inline(),
                                            DatePicker::make('quiz_expiry_date')
                                                ->placeholder(__('messages.quiz.quiz_expiry_date'))
                                                ->minDate(now()->format('Y-m-d'))
                                                ->label(__('messages.quiz.quiz_expiry_date') . ':')
                                                ->native(false)
                                                ->hintAction(
                                                    Action::make('clearDate')
                                                        ->iconButton()
                                                        ->icon('heroicon-o-x-circle')
                                                        ->tooltip(__('messages.common.clear_date'))
                                                        ->action(function (\Filament\Forms\Set $set) {
                                                            $set('quiz_expiry_date', null);
                                                        })
                                                ),
                                            ToggleButtons::make('time_configuration')
                                                ->label(__('messages.quiz.time_configuration') . ':')
                                                ->options([
                                                    1 => __('messages.common.enable'),
                                                    0 => __('messages.common.disable'),
                                                ])
                                                ->default(0)
                                                ->required()
                                                ->inline(),
                                            Toggle::make("add_manually_questions")
                                                ->inline(false)
                                                ->live()
                                                ->hidden(fn($operation) => $operation == 'edit')
                                                ->label(__('messages.quiz.add_manually_questions') . ':'),
                                            Section::make()
                                                ->schema([
                                                    TextInput::make('time')
                                                        ->numeric()
                                                        ->placeholder(__('messages.quiz.time'))
                                                        ->required()
                                                        ->minValue(1)
                                                        ->rules(['integer', 'min:1'])
                                                        ->label(__('messages.quiz.time_label') . ':')
                                                        ->extraAttributes([
                                                            'onkeydown' => "if(event.key === '-' || event.key === '+' || event.key === 'e'){ event.preventDefault(); }"
                                                        ]),

                                                    Radio::make('time_type')
                                                        ->options([
                                                            1 => __('messages.quiz.time_question'),
                                                            2 => __('messages.quiz.time_quiz'),
                                                        ])
                                                        ->required()
                                                        ->label(__('messages.quiz.time_type') . ':'),

                                                ])->live()->columns(2)->hidden(function ($get) {
                                                    return !$get('time_configuration');
                                                }),
                                        ])
                                        ->columns(2),
                                ])
                                ->columnSpan(1),
                        ])
                        ->columns(2),
                ]),

            Repeater::make('questions')
                ->label(__('messages.common.questions'))
                ->columnSpanFull()
                ->reorderableWithDragAndDrop()
                ->schema([
                    Hidden::make('is_editable')->dehydrated(false)->default(false)->formatStateUsing(fn($state) => $state ?? false),
                    Group::make()
                        ->live()
                        ->hidden(fn(Get $get) => $get('is_editable'))
                        ->schema(function (Get $get) {
                            $placeholderSchema = [
                                Placeholder::make('placeholder_title')
                                    ->dehydrated(false)
                                    ->label('')
                                    ->content(function (Get $get) {
                                        return new HtmlString("<div class='text-lg'>{$get('title')}</div>");
                                    })
                                    ->columnSpanFull(),
                            ];

                            $answers = $get('answers') ?? [];

                            $answerPlaceholders = collect($answers)
                                ->values()
                                ->map(function ($answer, $index) {
                                    $title = e($answer['title']);
                                    $isCorrect = $answer['is_correct'] ?? false;

                                    $icon = $isCorrect
                                        ? '<svg style="--c-500:var(--success-500);" class="w-7 h-7 text-custom-500" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 00-1.414 0L9 11.586 5.707 8.293a1 1 0 00-1.414 1.414l4 4a1 1 0 001.414 0l7-7a1 1 0 000-1.414z" clip-rule="evenodd" /></svg>'
                                        : '<svg style="--c-500:var(--danger-500);" class="w-7 h-7 text-custom-500" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 9l-3-3a1 1 0 10-1.414 1.414L8.586 10l-3 3a1 1 0 101.414 1.414L10 11.414l3 3a1 1 0 001.414-1.414L11.414 10l3-3A1 1 0 0013 5.586L10 9z" clip-rule="evenodd" /></svg>';

                                    return Placeholder::make("answer_{$index}")
                                        ->label('')
                                        ->dehydrated(false)
                                        ->content(new HtmlString("<div class='flex items-center gap-2'>{$icon}<span>{$title}</span></div>"));
                                })
                                ->toArray();

                            return array_merge($placeholderSchema, $answerPlaceholders);
                        })
                        ->columns(2),

                    TextInput::make('title')
                        ->visible(fn(Get $get) => $get('is_editable'))
                        ->label(__('messages.common.question') . ':')
                        ->validationAttribute(__('messages.common.question'))
                        ->required(),
                    Textarea::make('explanation')
                        ->rows(2)
                        ->visible(function ($get) {
                            return $get('../../quiz_mode') == 1 && $get('is_editable');
                        })
                        ->placeholder(__('messages.quiz.explanation'))
                        ->label(__('messages.quiz.explanation') . ':')
                        ->validationAttribute(__('messages.quiz.explanation')),
                    Repeater::make('answers')
                        ->live()
                        ->visible(fn(Get $get) => $get('is_editable'))
                        ->label(__('messages.common.answer') . ':')
                        ->addActionLabel(__('messages.common.add_answer'))
                        ->defaultItems(2)
                        ->minItems(2)
                        ->maxItems(4)
                        ->validationAttribute(__('messages.common.answer'))
                        ->grid(2)
                        ->schema([
                            Group::make([
                                TextInput::make('title')
                                    ->placeholder(__('messages.common.answer'))
                                    ->label(__('messages.common.answer') . ':')
                                    ->required()
                                    ->columnSpan(3),
                                Toggle::make('is_correct')
                                    ->inline(false)
                                    ->label(__('messages.common.is_correct') . ':'),
                            ])->columns(4),
                        ])
                        ->required(),
                ])
                ->extraItemActions([
                    Action::make('editAction')
                        ->icon(function (array $arguments, Repeater $component) {
                            $items = $component->getState();
                            $index = $arguments['item'];
                            return $items[$index]['is_editable'] ? 'heroicon-o-arrow-right-end-on-rectangle' : 'heroicon-o-pencil-square';
                        })
                        ->color('primary')
                        ->action(function (array $arguments, Repeater $component, \Filament\Forms\Set $set) {
                            $items = $component->getState();
                            $index = $arguments['item'];
                            if ($items[$index]['is_editable']) {
                                $items[$index]['is_editable'] = false;
                            } else {
                                $items[$index]['is_editable'] = true;
                            }
                            $set('questions', $items);
                        }),
                ])
                ->visible(fn(Get $get) => !empty($get('questions')))
                ->hidden(fn(string $operation): bool => $operation === 'create')
                ->addable(false),

            Repeater::make('custom_questions')
                ->columnSpanFull()
                ->label('')
                ->reorderableWithDragAndDrop(true)
                ->addActionLabel(__('messages.common.add_new_question'))
                ->hidden(function ($operation, $get) {
                    if ($operation === 'create') {
                        return empty($get('add_manually_questions')) ? true : false;
                    }
                })
                ->schema([
                    TextInput::make('title')
                        ->label(__('messages.common.question') . ':')
                        ->placeholder(__('messages.common.question'))
                        ->validationAttribute(__('messages.common.answer'))
                        ->required(),
                    Textarea::make('explanation')
                        ->rows(2)
                        ->visible(function ($get) {
                            return $get('../../quiz_mode') == 1;
                        })
                        ->placeholder(__('messages.quiz.explanation'))
                        ->label(__('messages.quiz.explanation') . ':')
                        ->validationAttribute(__('messages.quiz.explanation')),
                    Repeater::make('answers')
                        ->label(__('messages.common.answer') . ':')
                        ->addActionLabel(__('messages.common.add_answer'))
                        ->defaultItems(2)
                        ->minItems(2)
                        ->maxItems(4)
                        ->validationAttribute(__('messages.common.answer'))
                        ->grid(2)
                        ->schema([
                            Group::make([
                                TextInput::make('title')
                                    ->placeholder(__('messages.common.answer'))
                                    ->label(__('messages.common.answer') . ':')
                                    ->required()
                                    ->columnSpan(3),
                                Toggle::make('is_correct')
                                    ->inline(false)
                                    ->label(__('messages.common.is_correct') . ':'),
                            ])->columns(4),
                        ])
                        ->required(),
                ]),

        ];
    }
}
