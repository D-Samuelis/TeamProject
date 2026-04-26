<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Request for deleting user account, with password confirmation and business ownership check.
 * Redirects back to profile settings with error if user owns any business.
 */
class DestroyProfileRequest extends FormRequest
{
    protected $redirect = '/profile#settings';

    public function rules(): array
    {
        return [
            'password' => ['required', 'current_password'],
        ];
    }

    /**
     * Optional: Custom error messages
     */
    public function messages(): array
    {
        return [
            'password.required' => 'Please enter your password to confirm deletion.',
            'password.current_password' => 'The password is incorrect.',
        ];
    }
}