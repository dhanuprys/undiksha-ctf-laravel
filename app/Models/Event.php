<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
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
            Cache::forget('active_event_attributes');
        });

        static::deleted(function (Event $event) {
            Cache::forget('active_event_attributes');
        });
    }

    public function scopeActive(Builder $query): void
    {
        $query->where('is_active', true);
    }

    public function getSetting(string $key, mixed $default = null): mixed
    {
        $settings = Cache::rememberForever('event_'.$this->id.'_settings', function () {
            return $this->settings()->pluck('value', 'key')->toArray();
        });

        return array_key_exists($key, $settings) ? $settings[$key] : $default;
    }

    public static function getActiveEvent(): ?self
    {
        $attributes = Cache::rememberForever('active_event_attributes', function () {
            $event = self::where('is_active', true)->first();
            
            return $event ? $event->getAttributes() : null;
        });

        if ($attributes) {
            $event = new self();
            $event->setRawAttributes($attributes, true);
            $event->exists = true;
            
            return $event;
        }

        return null;
    }
}
