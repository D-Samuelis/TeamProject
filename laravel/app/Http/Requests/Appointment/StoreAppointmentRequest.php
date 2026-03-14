<?php

namespace App\Http\Requests\Appointment;

use Illuminate\Foundation\Http\FormRequest;

class StoreAppointmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        // Nothing to coerce — all fields come in cleanly from the JS fetch
    }

    public function rules(): array
    {
        return [
            'asset_id'   => 'required|integer|exists:assets,id',
            'service_id' => 'required|integer|exists:services,id',
            'date'       => 'required|date|after_or_equal:today',
            'start_at'   => ['required', 'regex:/^\d{2}:\d{2}$/'],
        ];
    }

    public function messages(): array
    {
        return [
            'date.after_or_equal' => 'You cannot book an appointment in the past.',
            'start_at.regex'      => 'Time must be in HH:MM format.',
        ];
    }
}
