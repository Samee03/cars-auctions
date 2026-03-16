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
            'phone' => ['required', 'string', 'max:30'],
            'country' => ['required', 'string', 'max:100'],
            'city' => ['required', 'string', 'max:100'],
            'address' => ['nullable', 'string', 'max:500'],
            'terms_accepted' => ['required', 'accepted'],
        ];

        // Company-specific fields
        if ($this->input('account_type') === 'company') {
            $rules = array_merge($rules, [
                'company_name' => ['required', 'string', 'max:255'],
                'registration_number' => ['nullable', 'string', 'max:100'],
                'company_phone' => ['required', 'string', 'max:30'],
                'company_country' => ['required', 'string', 'max:100'],
                'company_city' => ['required', 'string', 'max:100'],
                'company_address' => ['nullable', 'string', 'max:500'],
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
