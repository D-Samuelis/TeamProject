<?php

namespace App\Http\Requests\Branch;

use Illuminate\Foundation\Http\FormRequest;

class StoreBranchRequest extends FormRequest
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
            'is_active' => $this->boolean('is_active'),
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
            'name' => 'required|string|max:255',
            'type' => 'required|in:physical,online,hybrid',
            'address_line_1' => ['nullable', 'string', 'max:255', 'required_if:type,physical,hybrid'],
            'city' => ['nullable', 'string', 'max:255', 'required_if:type,physical,hybrid'],
            'postal_code' => ['nullable', 'string', 'max:50', 'required_if:type,physical,hybrid'],
            'country' => ['nullable', 'string', 'max:255', 'required_if:type,physical,hybrid'],
            'address_line_2' => 'nullable|string|max:255',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Custom error message
     */
    public function messages(): array
    {
        return [
            'address_line_1.required_if' => 'An address is required for physical or hybrid locations.',
            'city.required_if' => 'City is required for physical locations.',
        ];
    }
}
