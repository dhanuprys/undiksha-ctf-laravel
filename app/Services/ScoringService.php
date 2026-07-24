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
                $points = $this->calculatePenalty($lockedChallenge->event_id);
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
     * Calculate points with degradation logic.
     * Formula: base_score * (1 - degradation_rate) ^ solvedCount
     *
     * First solver gets full points, each subsequent solver gets less.
     */
    protected function calculateCorrectPoints(Challenge $challenge): int
    {
        // Count how many teams have already solved this challenge
        $solvedCount = Submission::where('challenge_id', $challenge->id)
            ->where('is_correct', true)
            ->count();

        // Determine degradation rate from event settings
        $degradationRate = Setting::where('event_id', $challenge->event_id)
            ->where('key', 'degradation_rate')
            ->value('value');

        // Default to 10% if not configured
        $degradationRate = $degradationRate !== null ? (float) $degradationRate : 0.10;

        $multiplier = pow(1 - $degradationRate, $solvedCount);

        // Minimum 10% of base score to prevent zero-point challenges
        $minScore = (int) ceil($challenge->base_score * 0.10);

        return max($minScore, (int) round($challenge->base_score * $multiplier));
    }

    /**
     * Calculate point deductions for incorrect flags.
     * Returns a negative number (or zero).
     */
    protected function calculatePenalty(?int $eventId): int
    {
        $penalty = Setting::where('event_id', $eventId)
            ->where('key', 'penalty_deduction')
            ->value('value');

        // Default to 0 penalty if not configured
        $penalty = $penalty !== null ? (int) $penalty : 0;

        return -1 * abs($penalty);
    }
}
