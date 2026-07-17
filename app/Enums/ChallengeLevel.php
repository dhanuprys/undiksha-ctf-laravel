<?php

namespace App\Enums;

enum ChallengeLevel: string
{
    case Easy = 'easy';
    case Medium = 'medium';
    case Hard = 'hard';
    case VeryHard = 'very_hard';
}
