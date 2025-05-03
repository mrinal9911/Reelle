<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreUserRequest extends FormRequest
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
            'nickname'            => 'nullable|string|max:255',
            // 'firstName'           => 'required|string|max:255',
            // 'lastName'            => 'required|string|max:255',
            'email'               => 'required|email|max:255|unique:users,email',
            'phone'               => 'nullable|string|max:255',
            'dob'                 => 'nullable|date',
            'gender'              => 'nullable|in:male,female,other',
            'occupation'          => 'nullable|string|max:255',
            'relationshipStatus'  => 'nullable|string|max:255',
            'primaryLanguage'     => 'nullable|string|max:255',
            'secondaryLanguage'   => 'nullable|string|max:255',
            'educationLevel'      => 'nullable|string|max:255',
            'netWorthRange'       => 'nullable|string|max:255',
            'idDocumentPath'      => 'nullable|string|max:255',
            'govt_verified'       => 'boolean',
            'two_fa_enabled'      => 'boolean',
            'device_logs'         => 'nullable|json',
            'email_verified_at'   => 'nullable|date',
            'password'            => 'required|string|min:8',
            'remember_token'      => 'nullable|string|max:100',
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
