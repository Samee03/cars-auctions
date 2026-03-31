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
            // User
            'name' => 'nullable|string|max:255|required_without:first_name',
            'first_name' => 'nullable|string|max:255|required_without:name',
            'last_name' => 'nullable|string|max:255',
            'email' => 'required|email',

            // Customer Profile
            'phone' => 'nullable|string|max:255',
            'company' => 'nullable|string|max:255',
            'date_of_birth' => 'nullable|date',
        ];
    }
}
