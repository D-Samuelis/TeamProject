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
        $userId = $this->route('user') ?? $this->user()?->id;
        $adultDate = now()->subYears(18)->format('Y-m-d');
        $oldestAllowedDate = now()->subYears(100)->format('Y-m-d');

        return [
            'current_password' => ['required', 'current_password'],
            'name'         => ['required', 'string', 'max:255'],
            'email'        => ['required', 'email', 'max:255', 'unique:users,email,' . $userId],
            'password'  => ['sometimes', 'nullable', 'confirmed', Password::defaults()],
            'city'         => ['required', 'string', 'max:255'],
            'country'      => ['required', 'string', 'max:255'],
            'title_prefix' => ['sometimes', 'nullable', 'string', 'max:50'],
            'birth_date'   => ['required', 'date_format:Y-m-d', 'before_or_equal:' . $adultDate, 'after:' . $oldestAllowedDate],
            'title_suffix' => ['sometimes', 'nullable', 'string', 'max:50'],
            'phone_number' => ['required', 'string', 'max:16', 'regex:/^\+[1-9]\d{7,14}$/'],
            'gender'       => ['sometimes', 'nullable', 'string', 'in:male,female,other,none'],
        ];
    }

    /**
     * Optional: Custom error messages
     */
    public function messages(): array
    {
        return [
            'gender.in' => 'The selected gender must be male, female, other, or unspecified.',
            'birth_date.date_format' => 'The birth date must match the format YYYY-MM-DD.',
            'birth_date.before_or_equal' => 'You must be at least 18 years old.',
            'birth_date.after' => 'Age must be under 100 years.',
            'phone_number.regex' => 'Use international format, e.g. +421901234567.',
            'current_password.required' => 'Please enter your current password to confirm changes.',
            'current_password.current_password' => 'The current password is incorrect.',
        ];
    }
}
