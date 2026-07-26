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
                'maxTeamSize' => 5,
            ]);
        }

        $maxTeamSize = (int) ($activeEvent->getSetting('max_team_size', 3));

        $team = $user->teams()
            ->where('event_id', $activeEvent->id)
            ->with(['users' => function ($query) {
                $query->select('users.id', 'name', 'email');
            }])
            ->withSum('submissions as total_score', 'points_awarded')
            ->withCount(['submissions as correct_submissions_count' => fn ($q) => $q->where('is_correct', true)])
            ->first();

        $submissions = null;
        if ($team) {
            $submissions = $team->submissions()
                ->with(['challenge:id,title', 'user:id,name'])
                ->orderByDesc('created_at')
                ->paginate(10);
        }

        return Inertia::render('competition/Team', [
            'team' => $team,
            'submissions' => $submissions,
            'maxTeamSize' => $maxTeamSize,
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

        // Check team size limit (default: 3 members)
        $maxTeamSize = (int) ($activeEvent->getSetting('max_team_size', 3));
        $currentMemberCount = $team->users()->count();

        if ($currentMemberCount >= $maxTeamSize) {
            return back()->with('flash', [
                'type' => 'error',
                'message' => 'Tim sudah penuh (maksimal '.$maxTeamSize.' anggota).',
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
