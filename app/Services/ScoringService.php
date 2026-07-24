<?php

namespace App\Services;

use App\Models\Challenge;
use App\Models\Setting;
use App\Models\Submission;
use App\Models\Team;
use Exception;
use Illuminate\Support\Facades\DB;

class ScoringService
{
    /**
     * Submit a flag for a challenge.
     * Handles race conditions using database locks.
     *
     * @throws Exception
     */
    public function submitFlag(Team $team, Challenge $challenge, int $userId, string $flag): Submission
    {
        return DB::transaction(function () use ($team, $challenge, $userId, $flag) {
            // Lock the challenge row to serialize concurrent submissions for the same challenge
            $lockedChallenge = Challenge::where('id', $challenge->id)->lockForUpdate()->firstOrFail();

            // Trim whitespace to prevent accidental mismatches
            $isCorrect = trim($flag) === trim($lockedChallenge->flag);

            if ($isCorrect) {
                // Check if already solved by this team (within the lock to prevent race conditions)
                $alreadySolved = Submission::where('challenge_id', $lockedChallenge->id)
                    ->where('team_id', $team->id)
                    ->where('is_correct', true)
                    ->exists();

                if ($alreadySolved) {
                    throw new Exception('Tantangan ini sudah diselesaikan oleh tim Anda.');
                }

                $points = $this->calculateCorrectPoints($lockedChallenge);
            } else {
                $points = $this->calculatePenalty($lockedChallenge->event_id, $lockedChallenge->base_score);
            }

            return Submission::create([
                'user_id' => $userId,
                'team_id' => $team->id,
                'challenge_id' => $lockedChallenge->id,
                'submitted_flag' => $flag,
                'is_correct' => $isCorrect,
                'points_awarded' => $points,
            ]);
        });
    }

    /**
     * Calculate points with step-down logic.
     *
     * First solver gets full base_score.
     * Second and subsequent solvers get base_score × (1 - degradation_rate).
     */
    protected function calculateCorrectPoints(Challenge $challenge): int
    {
        // Count how many teams have already solved this challenge
        $solvedCount = Submission::where('challenge_id', $challenge->id)
            ->where('is_correct', true)
            ->count();

        // First solver gets full points
        if ($solvedCount === 0) {
            return (int) $challenge->base_score;
        }

        // Determine degradation rate from event settings (default 10%)
        $degradationRate = Setting::where('event_id', $challenge->event_id)
            ->where('key', 'degradation_rate')
            ->value('value');

        $degradationRate = $degradationRate !== null ? (float) $degradationRate : 0.10;

        return (int) floor($challenge->base_score * (1 - $degradationRate));
    }

    /**
     * Calculate point deductions for incorrect flags.
     * Returns a negative number (or zero).
     *
     * Penalty = base_score × penalty_rate (default 5%).
     */
    protected function calculatePenalty(?int $eventId, int $baseScore): int
    {
        $penaltyRate = Setting::where('event_id', $eventId)
            ->where('key', 'penalty_deduction')
            ->value('value');

        // Default to 5% penalty if not configured
        $penaltyRate = $penaltyRate !== null ? (float) $penaltyRate : 0.05;

        if ($penaltyRate <= 0) {
            return 0;
        }

        return -1 * (int) floor($baseScore * $penaltyRate);
    }
}
