<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class RegisterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'name' => 'required',
            'surname' => 'required',
            'email' => ['required', 'email', Rule::unique('users', 'email')->whereNull('deleted_at')],
            'tel' => ['required', 'numeric', 'digits:8', Rule::unique('users', 'tel')->whereNull('deleted_at')],
            'password' => ['required', 'confirmed', Password::min(8)->letters()],
        ];
    }
}
