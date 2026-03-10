<?php

namespace App\Http\Requests\Branch;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBranchRequest extends FormRequest
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
            'id' => $this->route('branchId'),
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
            'name' => 'sometimes|string|max:255',
            'type' => 'sometimes|in:physical,online,hybrid',
            'address_line_1' => ['nullable', 'string', 'max:255', 'required_if:type,physical'],
            'address_line_2' => 'nullable|string|max:255',
            'city' => ['nullable', 'string', 'max:255', 'required_if:type,physical'],
            'postal_code' => ['nullable', 'string', 'max:50', 'required_if:type,physical'],
            'country' => ['nullable', 'string', 'max:255', 'required_if:type,physical'],
            'is_active' => 'nullable|boolean',
        ];
    }

    /**
     * Custom error message
     */
    public function messages(): array
    {
        return [
            'address_line_1.required_if' => 'An address is required for physical locations.',
        ];
    }
}
