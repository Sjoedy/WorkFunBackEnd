<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class GroupManagementRequest extends FormRequest
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
        $group = $this->route()->hasParameter('group') ? $this->route()->parameter('group') : null;
        $rules = $group
            ? [
                'name' => ['required', Rule::unique('groups', 'name')->whereNull('deleted_at')->ignore($group->id)]
            ]
            : [
                'name' => ['required', Rule::unique('groups', 'name')->whereNull('deleted_at')]
            ];
        $rules['description'] = 'required';
        return $rules;
    }
}
