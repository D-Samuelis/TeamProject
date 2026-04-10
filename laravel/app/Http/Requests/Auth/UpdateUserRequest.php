<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class UpdateUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Typically true, or check if the user has permission to update this profile
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        // We get the user ID from the route to ignore it during the unique email check
        $userId = $this->route('user') ?? $this->user()?->id;

        return [
            'current_password' => [
                'required_with:password,email',
                'current_password'
            ],
            'name'         => ['sometimes', 'string', 'max:255'],
            'email'        => ['sometimes', 'email', 'max:255', 'unique:users,email,' . $userId],
            'password'     => ['sometimes', 'confirmed', Password::defaults()],
            'city'         => ['sometimes', 'nullable', 'string', 'max:255'],
            'country'      => ['sometimes', 'nullable', 'string', 'max:255'],
            'title_prefix' => ['sometimes', 'nullable', 'string', 'max:50'],
            'birth_date'   => ['sometimes', 'nullable', 'date_format:Y-m-d'],
            'title_suffix' => ['sometimes', 'nullable', 'string', 'max:50'],
            'phone_number' => ['sometimes', 'nullable', 'string', 'max:20'],
            'gender'       => ['sometimes', 'nullable', 'string', 'in:male,female,other'],
        ];
    }

    /**
     * Optional: Custom error messages
     */
    public function messages(): array
    {
        return [
            'gender.in' => 'The selected gender must be male, female, or other.',
            'birth_date.date_format' => 'The birth date must match the format YYYY-MM-DD.',
        ];
    }
}
