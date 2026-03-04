<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

// Validation lives in FormRequest (HTTP concern). It is called by the AuthController to separate concerns.
class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'country' => 'required|string|max:100',
            'city' => 'required|string|max:100',
            'password' => 'required|string|min:8|confirmed',
            'password_confirmation' => 'required|string|min:8',

            'title_prefix' => 'nullable|string|max:50',
            'birth_date' => 'nullable|date',
            'title_suffix' => 'nullable|string|max:50',
            'phone_number' => 'nullable|string|max:20',
            'gender' => 'nullable|string|in:male,female,other, none',
        ];
    }
}
