<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateAddressRequest extends FormRequest
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
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'street' => 'string|max:255',
            'city' => 'string|max:255',
            'state' => 'string|max:255',
            'zip' => 'string|max:10',
            'country' => 'string|max:255',
            'type' => 'nullable|string|max:255',
            'is_default' => 'nullable|boolean',
            'notes' => 'nullable|string|max:255',
        ];
    }
}
