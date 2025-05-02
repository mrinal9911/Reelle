<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserProfileRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'nickname' => 'nullable|string|max:50',
            'first_name' => 'nullable|string|max:50',
            'last_name' => 'required|string|max:50',
            'phone' => 'nullable|string|max:20',
            'dob' => 'nullable|date|before:today',
            'gender' => 'nullable|in:male,female,other',
            'occupation' => 'nullable|string|max:100',
            'relationship_status' => 'nullable|string|max:50',
            'primary_language' => 'nullable|string|max:30',
            'secondary_language' => 'nullable|string|max:30',
            'education_level' => 'nullable|string|max:50',
            'net_worth_range' => 'nullable|string|max:50',
            'id_document' => 'nullable|file|mimes:jpeg,png,pdf|max:2048',
        ];
    }
}
