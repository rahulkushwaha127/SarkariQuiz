@extends('layouts.student')

@section('title', 'FAQ')

@section('content')
<div class="space-y-4">
    <div class="rounded-2xl border border-stone-200 bg-white p-4 shadow-sm">
        <h1 class="text-lg font-semibold text-stone-800">Frequently Asked Questions</h1>
        <p class="mt-1 text-sm text-stone-500">Quick answers about {{ $siteName ?? config('app.name', 'QuizWhiz') }}.</p>
    </div>

    <div class="space-y-3">
        <div class="rounded-2xl border border-stone-200 bg-white p-4 shadow-sm">
            <h2 class="text-sm font-semibold text-stone-800">What is this platform?</h2>
            <p class="mt-2 text-sm text-stone-600">This is a quiz and practice platform for exam aspirants. You can attempt subject-wise quizzes, previous year questions (PYQ), daily challenges, and join live contests to test yourself and track your progress.</p>
        </div>

        <div class="rounded-2xl border border-stone-200 bg-white p-4 shadow-sm">
            <h2 class="text-sm font-semibold text-stone-800">Do I need an account to practice?</h2>
            <p class="mt-2 text-sm text-stone-600">You can browse exams and quizzes as a guest. To save progress, earn XP, maintain streaks, join contests, and access features like Practice, PYQ Bank, and Revision, please register or log in (e.g. with Google).</p>
        </div>

        <div class="rounded-2xl border border-stone-200 bg-white p-4 shadow-sm">
            <h2 class="text-sm font-semibold text-stone-800">What is Practice mode?</h2>
            <p class="mt-2 text-sm text-stone-600">Practice lets you pick a subject and topic (or leave them empty for random questions), choose difficulty, and attempt multiple-choice questions. Your answers are checked instantly and you can continue without full page reloads.</p>
        </div>

        <div class="rounded-2xl border border-stone-200 bg-white p-4 shadow-sm">
            <h2 class="text-sm font-semibold text-stone-800">What is the PYQ Bank?</h2>
            <p class="mt-2 text-sm text-stone-600">PYQ Bank contains previous year questions from exams. You can filter by exam, year, and paper, and practice with real exam-style MCQs to prepare better.</p>
        </div>

        <div class="rounded-2xl border border-stone-200 bg-white p-4 shadow-sm">
            <h2 class="text-sm font-semibold text-stone-800">How do contests work?</h2>
            <p class="mt-2 text-sm text-stone-600">Contests are time-bound quizzes. You can browse public contests or use “Join Contest” to enter with a code. Complete the quiz before the deadline to appear on the leaderboard and see your rank.</p>
        </div>

        <div class="rounded-2xl border border-stone-200 bg-white p-4 shadow-sm">
            <h2 class="text-sm font-semibold text-stone-800">What is the Daily Challenge?</h2>
            <p class="mt-2 text-sm text-stone-600">Daily Challenge is a set of questions that refresh every day. Solving them helps you build a habit and can count towards your streak and XP.</p>
        </div>

        <div class="rounded-2xl border border-stone-200 bg-white p-4 shadow-sm">
            <h2 class="text-sm font-semibold text-stone-800">What are streaks and XP?</h2>
            <p class="mt-2 text-sm text-stone-600">When you complete at least one quiz or practice in a day, you maintain a streak. XP (experience points) are earned from activity and level you up. Both are shown on your dashboard when you are logged in.</p>
        </div>

        <div class="rounded-2xl border border-stone-200 bg-white p-4 shadow-sm">
            <h2 class="text-sm font-semibold text-stone-800">What are Batches and Plans?</h2>
            <p class="mt-2 text-sm text-stone-600">Batches are groups you may be added to by a creator or institute. Plans are subscription tiers (e.g. Free, Premium) that can unlock extra content or features. You can see “My Batches” in the menu if you are in a batch, and “Plans” to view or upgrade your subscription.</p>
        </div>

        <div class="rounded-2xl border border-stone-200 bg-white p-4 shadow-sm">
            <h2 class="text-sm font-semibold text-stone-800">How do I change my profile or language?</h2>
            <p class="mt-2 text-sm text-stone-600">Open the menu and go to “My Profile”. There you can update your name and set a default language for content (e.g. English). Quizzes, PYQ, and other content will be filtered by your chosen language where supported.</p>
        </div>

        <div class="rounded-2xl border border-stone-200 bg-white p-4 shadow-sm">
            <h2 class="text-sm font-semibold text-stone-800">I found an error in a question. What should I do?</h2>
            <p class="mt-2 text-sm text-stone-600">Use the Contact page to send us the quiz or question details and a short description of the error. We will review and correct it where possible.</p>
        </div>
    </div>
</div>
@endsection
