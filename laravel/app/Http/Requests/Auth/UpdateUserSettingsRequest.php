<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserSettingsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'notify_email'    => ['sometimes', 'boolean'],
            'notify_sms'      => ['sometimes', 'boolean'],
            'is_visible' => ['sometimes', 'boolean'],
        ];
    }
}