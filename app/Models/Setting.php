<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class Setting extends Model
{
    use HasFactory, LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logUnguarded();
    }

    protected $fillable = [
        'event_id',
        'key',
        'value',
    ];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    protected static function boot(): void
    {
        parent::boot();

        static::saved(function (Setting $setting) {
            Cache::forget('event_'.$setting->event_id.'_settings');
        });

        static::deleted(function (Setting $setting) {
            Cache::forget('event_'.$setting->event_id.'_settings');
        });
    }
}
