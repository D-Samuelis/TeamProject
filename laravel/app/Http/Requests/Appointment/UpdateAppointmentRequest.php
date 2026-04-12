<?php

namespace App\Http\Requests\Appointment;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAppointmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_active' => $this->boolean('is_active'),
        ]);
    }

    public function rules(): array
    {
        return [
            'date'     => 'sometimes|date',
            'start_at' => 'sometimes|date_format:H:i',
            'status'   => 'sometimes|in:pending,confirmed,cancelled',
        ];
    }
}
