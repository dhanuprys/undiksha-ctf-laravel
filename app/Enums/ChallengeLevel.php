<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum ChallengeLevel: string implements HasColor, HasLabel
{
    case Easy = 'easy';
    case Medium = 'medium';
    case Hard = 'hard';
    case VeryHard = 'very_hard';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::Easy => 'Easy',
            self::Medium => 'Medium',
            self::Hard => 'Hard',
            self::VeryHard => 'Very Hard',
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::Easy => 'success',
            self::Medium => 'warning',
            self::Hard => 'danger',
            self::VeryHard => 'danger',
        };
    }
}
