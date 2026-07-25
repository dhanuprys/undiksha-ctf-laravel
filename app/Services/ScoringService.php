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

                $solvedCount = Submission::where('challenge_id', $lockedChallenge->id)
                    ->where('is_correct', true)
                    ->count();

                $points = $this->calculateDynamicScore(
                    $lockedChallenge->base_score,
                    $solvedCount,
                    $this->getDegradationRate($lockedChallenge->event_id),
                );
            } else {
                $points = $this->calculatePenalty(
                    $lockedChallenge->base_score,
                    $this->getPenaltyRate($lockedChallenge->event_id),
                );
            }

            return Submission::create([
                'user_id' => $userId,
                'team_id' => $team->id,
                'challenge_id' => $lockedChallenge->id,
                'submitted_flag' => $flag,
                'is_correct' => $isCorrect,
                'points_awarded' => $points,
            ]);
        }, 3);
    }

    /**
     * Calculate the dynamic score for a challenge based on how many teams have solved it.
     *
     * First solver gets the full base_score.
     * Subsequent solvers get base_score × (1 - degradationRate).
     */
    public function calculateDynamicScore(int $baseScore, int $solvedCount, float $degradationRate): int
    {
        if ($solvedCount === 0) {
            return $baseScore;
        }

        return (int) floor($baseScore * (1 - $degradationRate));
    }

    /**
     * Calculate point deductions for incorrect flags.
     * Returns a negative number (or zero).
     *
     * Penalty = base_score × penaltyRate.
     */
    public function calculatePenalty(int $baseScore, float $penaltyRate): int
    {
        if ($penaltyRate <= 0) {
            return 0;
        }

        return -1 * (int) floor($baseScore * $penaltyRate);
    }

    /**
     * Get the degradation rate for an event from its settings.
     * Defaults to 10% if not configured.
     */
    public function getDegradationRate(?int $eventId): float
    {
        $value = Setting::where('event_id', $eventId)
            ->where('key', 'degradation_rate')
            ->value('value');

        return $value !== null ? (float) $value : 0.10;
    }

    /**
     * Get the penalty rate for an event from its settings.
     * Defaults to 5% if not configured.
     */
    public function getPenaltyRate(?int $eventId): float
    {
        $value = Setting::where('event_id', $eventId)
            ->where('key', 'penalty_deduction')
            ->value('value');

        return $value !== null ? (float) $value : 0.05;
    }
}
