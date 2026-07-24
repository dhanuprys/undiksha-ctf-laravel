<?php

use App\Http\Controllers\AttachmentController;
use App\Http\Controllers\ChallengeController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LeaderboardController;
use App\Http\Controllers\SubmissionController;
use App\Http\Controllers\TeamController;
use Illuminate\Support\Facades\Route;

Route::inertia('/', 'Welcome')->name('home');

Route::middleware(['auth', 'verified', 'participant'])->group(function () {
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Team
    Route::get('team', [TeamController::class, 'show'])->name('team.show');
    Route::post('team/join', [TeamController::class, 'join'])
        ->middleware('throttle:10,1')
        ->name('team.join');

    // Challenges
    Route::get('challenges', [ChallengeController::class, 'index'])->name('challenges.index');
    Route::get('challenges/{challenge}', [ChallengeController::class, 'show'])->name('challenges.show');

    // Submissions
    Route::post('submissions', [SubmissionController::class, 'store'])
        ->middleware('throttle:10,1')
        ->name('submissions.store');

    // Leaderboard
    Route::get('leaderboard', [LeaderboardController::class, 'index'])->name('leaderboard.index');

    // Attachment download (signed)
    Route::get('attachments/{attachment}/download', [AttachmentController::class, 'download'])
        ->middleware('signed')
        ->name('attachments.download');
});

require __DIR__.'/settings.php';
