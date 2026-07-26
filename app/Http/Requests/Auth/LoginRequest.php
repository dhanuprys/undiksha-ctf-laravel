<?php

namespace App\Http\Requests\Auth;

use App\Rules\TurnstileRule;
use Laravel\Fortify\Http\Requests\LoginRequest as FortifyLoginRequest;

class LoginRequest extends FortifyLoginRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules()
    {
        return array_merge(parent::rules(), [
            'cf-turnstile-response' => ['required', 'string', new TurnstileRule],
        ]);
    }
}
