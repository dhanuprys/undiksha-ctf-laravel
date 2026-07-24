<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class JoinTeamRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Authorized by auth middleware
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'join_code' => ['required', 'string', 'max:20'],
        ];
    }

    public function messages(): array
    {
        return [
            'join_code.exists' => 'Kode tim tidak valid atau tidak ditemukan.',
        ];
    }
}
