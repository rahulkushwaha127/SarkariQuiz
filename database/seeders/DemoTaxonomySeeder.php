<?php

namespace Database\Seeders;

use App\Models\Exam;
use App\Models\Subject;
use App\Models\Topic;
use App\Support\Slug;
use Illuminate\Database\Seeder;

class DemoTaxonomySeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            [
                'name' => 'SSC',
                'position' => 10,
                'subjects' => [
                    ['name' => 'General Knowledge', 'position' => 10, 'topics' => ['History', 'Geography', 'Polity', 'Economy', 'Current Affairs']],
                    ['name' => 'Reasoning', 'position' => 20, 'topics' => ['Analogies', 'Series', 'Blood Relations', 'Coding-Decoding']],
                    ['name' => 'Quantitative Aptitude', 'position' => 30, 'topics' => ['Percentages', 'Ratio & Proportion', 'Time & Work', 'Profit & Loss']],
                    ['name' => 'English', 'position' => 40, 'topics' => ['Grammar', 'Vocabulary', 'Reading Comprehension']],
                ],
            ],
            [
                'name' => 'Railways',
                'position' => 20,
                'subjects' => [
                    ['name' => 'General Science', 'position' => 10, 'topics' => ['Physics', 'Chemistry', 'Biology']],
                    ['name' => 'Mathematics', 'position' => 20, 'topics' => ['Algebra', 'Geometry', 'Trigonometry']],
                ],
            ],
            [
                'name' => 'Banking',
                'position' => 30,
                'subjects' => [
                    ['name' => 'Banking Awareness', 'position' => 10, 'topics' => ['RBI', 'Financial Terms', 'Digital Banking']],
                    ['name' => 'Computer Awareness', 'position' => 20, 'topics' => ['Basics', 'MS Office', 'Internet']],
                ],
            ],
        ];

        foreach ($data as $examRow) {
            $exam = Exam::query()->updateOrCreate(
                ['slug' => Slug::make($examRow['name'])],
                [
                    'name' => $examRow['name'],
                    'is_active' => true,
                    'position' => (int) ($examRow['position'] ?? 0),
                ]
            );

            foreach ($examRow['subjects'] as $subjectRow) {
                $subject = Subject::query()->updateOrCreate(
                    ['exam_id' => $exam->id, 'slug' => Slug::make($subjectRow['name'])],
                    [
                        'name' => $subjectRow['name'],
                        'is_active' => true,
                        'position' => (int) ($subjectRow['position'] ?? 0),
                    ]
                );

                foreach ($subjectRow['topics'] as $idx => $topicName) {
                    Topic::query()->updateOrCreate(
                        ['subject_id' => $subject->id, 'slug' => Slug::make($topicName)],
                        [
                            'name' => $topicName,
                            'is_active' => true,
                            'position' => (int) (($idx + 1) * 10),
                        ]
                    );
                }
            }
        }
    }
}

