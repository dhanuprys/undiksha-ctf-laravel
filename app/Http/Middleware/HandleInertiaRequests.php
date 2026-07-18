<?php

namespace App\Http\Middleware;

use App\Models\Event;
use Illuminate\Http\Request;
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

        $activeEvent = Event::where('is_active', true)->first();

        $currentTeam = null;
        if ($user && $activeEvent && ! $user->isAdmin()) {
            $currentTeam = $user->teams()->where('event_id', $activeEvent->id)->first();
        }

        return [
            ...parent::share($request),
            'name' => config('app.name'),
            'activeEvent' => $activeEvent,
            'auth' => [
                'user' => $user ? array_merge($user->toArray(), [
                    'current_team' => $currentTeam,
                ]) : null,
            ],
            'sidebarOpen' => ! $request->hasCookie('sidebar_state') || $request->cookie('sidebar_state') === 'true',
        ];
    }
}
