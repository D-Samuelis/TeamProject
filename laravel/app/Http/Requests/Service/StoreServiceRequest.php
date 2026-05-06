<?php

namespace App\Http\Requests\Service;

use Illuminate\Foundation\Http\FormRequest;

class StoreServiceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'business_id' => $this->input('business_id'),
            'is_active' => $this->boolean('is_active'),
            'requires_manual_acceptance' => $this->boolean('requires_manual_acceptance'),
            'cancellation_period' => $this->input('cancellation_period'),
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'business_id' => 'required|exists:businesses,id',
            'category_id' => 'nullable|integer|exists:categories,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'duration_minutes' => 'required|integer|min:1',
            'price' => 'required|numeric|min:0',
            'location_type' => 'nullable|in:branch,online,hybrid',
            'is_active' => 'boolean',
            'requires_manual_acceptance' => 'boolean',
            'cancellation_period' => ['nullable', 'string', 'regex:/^(\d+\s*[wdhm]\s*)+$/i'],
            'branch_ids' => 'array',
            'branch_ids.*' => 'exists:branches,id',
        ];
    }
}
