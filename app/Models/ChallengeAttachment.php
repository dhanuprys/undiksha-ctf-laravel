<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChallengeAttachment extends Model
{
    protected $fillable = [
        'challenge_id',
        'file_name',
        'file_path',
        'download_count',
    ];

    public function challenge(): BelongsTo
    {
        return $this->belongsTo(Challenge::class);
    }
}
