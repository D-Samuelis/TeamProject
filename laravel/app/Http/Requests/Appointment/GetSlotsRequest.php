<?php

namespace App\Http\Requests\Appointment;

use Illuminate\Foundation\Http\FormRequest;

class GetSlotsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        // Default to current month if not provided
        $this->mergeIfMissing([
            'from' => now()->startOfMonth()->toDateString(),
            'to'   => now()->endOfMonth()->toDateString(),
        ]);
    }

    public function rules(): array
    {
        return [
            'asset_id'   => 'required|integer|exists:assets,id',
            'service_id' => 'required|integer|exists:branch_service,id',
            'from'       => 'required|date',
            'to'         => 'required|date|after_or_equal:from',
        ];
    }
}
