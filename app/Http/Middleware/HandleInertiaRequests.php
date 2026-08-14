<?php

namespace App\Http\Middleware;

use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        $user = $request->user();

        $activeEvent = Event::getActiveEvent();

        $currentTeam = null;
        if ($user && $activeEvent && ! $user->isAdmin()) {
            $teamAttributes = Cache::remember("user_{$user->id}_team_event_{$activeEvent->id}", 60, function () use ($user, $activeEvent) {
                $team = $user->teams()->where('event_id', $activeEvent->id)->first();
                
                return $team ? $team->getAttributes() : null;
            });

            if ($teamAttributes) {
                $currentTeam = new \App\Models\Team();
                $currentTeam->setRawAttributes($teamAttributes, true);
                $currentTeam->exists = true;
            }
        }

        return [
            ...parent::share($request),
            'name' => config('app.name'),
            'activeEvent' => $activeEvent ? [
                'id' => $activeEvent->id,
                'name' => $activeEvent->name,
                'year' => $activeEvent->year,
                'start_time' => $activeEvent->start_time?->toIso8601String(),
                'end_time' => $activeEvent->end_time?->toIso8601String(),
                'is_active' => $activeEvent->is_active,
            ] : null,
            'serverTime' => now()->toIso8601String(),
            'serverTimezone' => config('app.timezone'),
            'auth' => [
                'user' => $user ? array_merge($user->toArray(), [
                    'current_team' => $currentTeam,
                ]) : null,
            ],
            'sidebarOpen' => ! $request->hasCookie('sidebar_state') || $request->cookie('sidebar_state') === 'true',
        ];
    }
}
