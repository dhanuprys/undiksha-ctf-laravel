<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class Team extends Model
{
    use HasFactory, LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logUnguarded();
    }

    protected $fillable = [
        'event_id',
        'name',
        'join_code',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($team) {
            if (empty($team->join_code)) {
                $team->join_code = Str::upper(Str::random(8));
            }
        });
    }

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class);
    }

    public function submissions()
    {
        return $this->hasMany(Submission::class);
    }

    public function getTotalScoreAttribute(): int
    {
        return $this->submissions()->sum('points_awarded');
    }
}
