<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class SubmitFlagRequest extends FormRequest
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
            'challenge_id' => ['required', 'exists:challenges,id'],
            'flag' => ['required', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'challenge_id.required' => 'Tantangan tidak valid.',
            'challenge_id.exists' => 'Tantangan tidak ditemukan.',
            'flag.required' => 'Flag tidak boleh kosong.',
        ];
    }
}
