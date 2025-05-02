<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'nickname'           => 'nullable|string|max:50',
            'firstName'          => 'nullable|string|max:50',
            'lastName'           => 'required|string|max:50',
            'phone'              => 'nullable|string|max:20',
            'dob'                => 'nullable|date|before:today',
            'gender'             => 'nullable|in:male,female,other',
            'occupation'         => 'nullable|string|max:100',
            'relationshipStatus' => 'nullable|string|max:50',
            'primaryLanguage'    => 'nullable|string|max:30',
            'secondaryLanguage'  => 'nullable|string|max:30',
            'educationLevel'     => 'nullable|string|max:50',
            'netWorthRange'      => 'nullable|string|max:50',
            'idDocumentPath'     => 'nullable|file|mimes:jpeg,png,pdf|max:2048',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'status' => false,
            'message' => 'Validation Error',
            'errors'  => $validator->errors()
        ], 200));
    }
}
