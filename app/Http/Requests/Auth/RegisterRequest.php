<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [
            'account_type' => ['required', 'in:individual,company'],
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email', 'max:255'],
            'password' => ['required', 'confirmed', Password::min(8)->mixedCase()->numbers()->symbols()],
            'phone' => ['nullable', 'string', 'max:30'],
            'terms_accepted' => ['required', 'accepted'],
        ];

        // Company-specific fields
        if ($this->input('account_type') === 'company') {
            $rules = array_merge($rules, [
                'company_name' => ['required', 'string', 'max:255'],
                'registration_number' => ['nullable', 'string', 'max:100'],
                'company_phone' => ['required', 'string', 'max:30'],
                'company_address_id' => ['nullable', 'integer', 'exists:addresses,id'],
                'company_address' => ['nullable', 'string', 'max:500'],
                'contact_first_name' => ['nullable', 'string', 'max:255'],
                'contact_last_name' => ['nullable', 'string', 'max:255'],
                'contact_email' => ['nullable', 'email', 'max:255'],
                'contact_phone' => ['nullable', 'string', 'max:30'],
            ]);
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'terms_accepted.accepted' => 'You must agree to the terms and conditions.',
            'password.confirmed' => 'Password confirmation does not match.',
        ];
    }
}
