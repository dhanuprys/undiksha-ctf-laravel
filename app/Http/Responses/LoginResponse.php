<?php

namespace App\Http\Responses;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;
use Symfony\Component\HttpFoundation\Response;

class LoginResponse implements LoginResponseContract
{
    /**
     * Create an HTTP response that represents the object.
     *
     * @param  Request  $request
     * @return Response
     */
    public function toResponse($request)
    {
        if (Auth::user()->isAdmin()) {
            return Inertia::location(url('/admin'));
        }
        $intendedUrl = session()->pull('url.intended', config('fortify.home', '/dashboard'));

        // Prevent non-admins from being redirected to the admin panel
        if (Str::startsWith($intendedUrl, url('/admin')) || Str::startsWith($intendedUrl, '/admin')) {
            $intendedUrl = config('fortify.home', '/dashboard');
        }

        return redirect()->to($intendedUrl);
    }
}
