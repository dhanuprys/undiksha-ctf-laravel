<?php

namespace App\Http\Controllers;

use App\Models\ChallengeAttachment;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AttachmentController extends Controller
{
    public function download(Request $request, ChallengeAttachment $attachment)
    {
        $user = $request->user();
        $activeEvent = Event::where('is_active', true)->first();

        if (! $activeEvent) {
            abort(404);
        }

        if ($activeEvent->start_time && now()->lt($activeEvent->start_time)) {
            return redirect()->route('dashboard')->with('error', 'Kompetisi belum dimulai.');
        }

        if ($activeEvent->end_time && now()->gt($activeEvent->end_time)) {
            return redirect()->route('dashboard')->with('error', 'Kompetisi sudah berakhir.');
        }

        $currentTeam = $user->teams()->where('event_id', $activeEvent->id)->first();
        if (! $currentTeam) {
            return redirect()->route('dashboard')->with('error', 'Anda belum bergabung dalam tim.');
        }

        // Validate attachment belongs to an active challenge in the active event
        $attachment->load('challenge');
        if ($attachment->challenge->event_id !== $activeEvent->id || ! $attachment->challenge->is_active) {
            abort(404);
        }

        if (! Storage::disk('local')->exists($attachment->file_path)) {
            abort(404, 'File tidak ditemukan.');
        }

        $attachment->increment('download_count');

        return Storage::disk('local')->download($attachment->file_path, $attachment->file_name);
    }
}
