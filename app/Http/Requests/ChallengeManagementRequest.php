<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ChallengeManagementRequest extends FormRequest
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
            'title' => 'required',
            'description' => 'required',
            'type' => ['required', Rule::in(['task', 'activity'])],
            'point' => ['required', 'numeric', Rule::in([25, 50, 75, 100])],
            'users' => ['required', 'array'],
            'users.*' => ['required', Rule::exists('users', 'id')],
        ];
    }
}
