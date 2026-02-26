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
            'password' => 'required|string|min:6|confirmed',
        ];
    }
}
