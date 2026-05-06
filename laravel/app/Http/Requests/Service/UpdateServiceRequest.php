<?php

namespace App\Http\Requests\Service;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateServiceRequest extends FormRequest
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
            'id' => $this->route('serviceId'),
            'is_active' => $this->has('is_active') ? $this->boolean('is_active') : null,
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
            'id' => 'required|exists:services,id',
            'category_id' => 'nullable|integer|exists:categories,id',

            'name' => 'sometimes|string|max:255',
            'description' => 'nullable|string|max:1000',
            'duration_minutes' => 'sometimes|integer|min:1',
            'price' => 'sometimes|numeric|min:0',
            'requires_manual_acceptance' => 'boolean',
            'cancellation_period' => ['nullable', 'string', 'regex:/^(\d+\s*[wdhm]\s*)+$/i'],
            'location_type' => ['sometimes', Rule::in(['branch', 'online', 'hybrid'])],
            'is_active' => 'nullable|boolean',

            'branch_ids' => 'sometimes|array',
            'branch_ids.*' => [
                'integer',
                Rule::exists('branches', 'id')->where(function ($query) {
                    $query->where('business_id', $this->input('business_id'));
                }),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'branch_ids.*.exists' => 'One or more selected branches are invalid for this business.',
        ];
    }
}
