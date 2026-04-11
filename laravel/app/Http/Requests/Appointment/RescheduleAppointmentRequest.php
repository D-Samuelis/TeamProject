<?php

namespace App\Http\Requests\Appointment;

use Illuminate\Foundation\Http\FormRequest;

class RescheduleAppointmentRequest extends FormRequest
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
            'date'     => 'sometimes|date',
            'start_at' => 'sometimes|date_format:H:i',
        ];
    }
}
