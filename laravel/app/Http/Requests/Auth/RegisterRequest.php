<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $adultDate = now()->subYears(18)->format('Y-m-d');
        $oldestAllowedDate = now()->subYears(100)->format('Y-m-d');

        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'country' => 'required|string|max:100',
            'city' => 'required|string|max:100',
            'password' => 'required|string|min:8|confirmed',
            'password_confirmation' => 'required|string|min:8',

            'title_prefix' => 'nullable|string|max:50',
            'birth_date' => ['required', 'date', 'before_or_equal:' . $adultDate, 'after:' . $oldestAllowedDate],
            'title_suffix' => 'nullable|string|max:50',
            'phone_number' => ['required', 'string', 'max:16', 'regex:/^\+[1-9]\d{7,14}$/'],
            'gender' => 'nullable|string|in:male,female,other,none',
        ];
    }

    public function messages(): array
    {
        return [
            'birth_date.before_or_equal' => 'You must be at least 18 years old.',
            'birth_date.after' => 'Age must be under 100 years.',
            'phone_number.regex' => 'Use international format, e.g. +421901234567.',
        ];
    }
}
