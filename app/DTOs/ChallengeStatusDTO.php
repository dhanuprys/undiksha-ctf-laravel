<?php

namespace App\DTOs;

class ChallengeStatusDTO
{
    public function __construct(
        public int $id,
        public string $title,
        public ?string $description,
        public int $currentScore,
        public string $difficulty,
        public bool $isSolvedByTeam,
        public bool $isFirstBlood
    ) {}
}
