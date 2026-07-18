<?php

namespace App\Http\Controllers;

use App\Http\Requests\JoinTeamRequest;
use App\Models\Event;
use App\Models\Team;
use Illuminate\Http\Request;
use Inertia\Inertia;

class TeamController extends Controller
{
    public function show(Request $request)
    {
        $user = $request->user();
        $activeEvent = Event::where('is_active', true)->first();

        if (! $activeEvent) {
            return Inertia::render('competition/Team', [
                'team' => null,
            ]);
        }

        $team = $user->teams()
            ->where('event_id', $activeEvent->id)
            ->with(['users' => function ($query) {
                $query->select('users.id', 'name', 'email');
            }])
            ->first();

        if ($team) {
            $team->load(['submissions.challenge:id,title']);
        }

        return Inertia::render('competition/Team', [
            'team' => $team,
        ]);
    }

    public function join(JoinTeamRequest $request)
    {
        $user = $request->user();
        $activeEvent = Event::where('is_active', true)->first();

        if (! $activeEvent) {
            return back()->with('flash', [
                'type' => 'error',
                'message' => 'Tidak ada acara yang sedang aktif.',
            ]);
        }

        // Check if user already has a team in this event
        $existingTeam = $user->teams()->where('event_id', $activeEvent->id)->first();
        if ($existingTeam) {
            return back()->with('flash', [
                'type' => 'error',
                'message' => 'Anda sudah bergabung dalam tim untuk acara ini.',
            ]);
        }

        $team = Team::where('join_code', $request->join_code)
            ->where('event_id', $activeEvent->id)
            ->first();

        if (! $team) {
            return back()->withErrors([
                'join_code' => 'Kode tim tidak ditemukan di acara ini.',
            ]);
        }

        // Attach user to team
        $user->teams()->attach($team->id);

        return back()->with('flash', [
            'type' => 'success',
            'message' => 'Berhasil bergabung dengan tim '.$team->name,
        ]);
    }
}
