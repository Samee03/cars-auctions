<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

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

            // Company buyers (optional partial update; omit key to leave unchanged)
            'company_profile' => ['sometimes', 'array'],
            'company_profile.company_name' => ['sometimes', 'nullable', 'string', 'min:1', 'max:255'],
            'company_profile.registration_number' => ['sometimes', 'nullable', 'string', 'max:100'],
            'company_profile.company_phone' => ['sometimes', 'nullable', 'string', 'max:30'],
            'company_profile.company_address' => ['sometimes', 'nullable', 'string', 'max:500'],
            'company_profile.contact_first_name' => ['sometimes', 'nullable', 'string', 'max:255'],
            'company_profile.contact_last_name' => ['sometimes', 'nullable', 'string', 'max:255'],
            'company_profile.contact_email' => ['sometimes', 'nullable', 'email', 'max:255'],
            'company_profile.contact_phone' => ['sometimes', 'nullable', 'string', 'max:30'],
            'company_profile.company_address_id' => [
                'sometimes',
                'nullable',
                'integer',
                Rule::exists('addresses', 'id')->where(function ($query): void {
                    $query->whereHas('users', fn ($q) => $q->where('users.id', $this->user()->id));
                }),
            ],
        ];
    }
}
