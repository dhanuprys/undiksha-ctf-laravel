<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Submission extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'team_id',
        'challenge_id',
        'submitted_flag',
        'is_correct',
        'points_awarded',
    ];

    protected $casts = [
        'is_correct' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function team()
    {
        return $this->belongsTo(Team::class);
    }

    public function challenge()
    {
        return $this->belongsTo(Challenge::class);
    }

    public function scopeCorrect(Builder $query): void
    {
        $query->where('is_correct', true);
    }

    public function scopeIncorrect(Builder $query): void
    {
        $query->where('is_correct', false);
    }
}
