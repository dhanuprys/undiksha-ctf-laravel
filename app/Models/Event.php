<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class Event extends Model
{
    use HasFactory, LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logUnguarded();
    }

    protected $fillable = [
        'name',
        'year',
        'is_active',
        'start_time',
        'end_time',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'start_time' => 'datetime',
        'end_time' => 'datetime',
    ];

    public function teams()
    {
        return $this->hasMany(Team::class);
    }

    public function challenges()
    {
        return $this->hasMany(Challenge::class);
    }

    public function settings()
    {
        return $this->hasMany(Setting::class);
    }

    protected static function boot(): void
    {
        parent::boot();

        // Ensure only one event can be active at a time
        static::saving(function (Event $event) {
            if ($event->is_active) {
                Event::where('id', '!=', $event->id ?? 0)
                    ->where('is_active', true)
                    ->update(['is_active' => false]);
            }
        });
    }

    public function scopeActive(Builder $query): void
    {
        $query->where('is_active', true);
    }

    public function getSetting(string $key, mixed $default = null): mixed
    {
        $setting = $this->settings()->where('key', $key)->first();

        return $setting ? $setting->value : $default;
    }
}
