<?php

namespace App\Models;

use App\Enums\ChallengeLevel;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class Challenge extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'event_id',
        'category_id',
        'title',
        'description',
        'base_score',
        'difficulty',
        'flag',
        'attachment_path',
        'is_active',
    ];

    protected $casts = [
        'difficulty' => ChallengeLevel::class,
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll();
    }

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function submissions()
    {
        return $this->hasMany(Submission::class);
    }

    public function attachments()
    {
        return $this->hasMany(ChallengeAttachment::class);
    }

    protected static function boot()
    {
        parent::boot();

        static::saved(function (Challenge $challenge) {
            Cache::forget('event_'.$challenge->event_id.'_categories_challenges');
        });

        static::deleted(function (Challenge $challenge) {
            Cache::forget('event_'.$challenge->event_id.'_categories_challenges');
        });
    }

    public function getCorrectSubmissionsCountAttribute(): int
    {
        return $this->submissions()->where('is_correct', true)->count();
    }

    public function scopeActive(Builder $query): void
    {
        $query->where('is_active', true);
    }
}
