<?php

namespace App\Http\Requests\Branch;

use Illuminate\Foundation\Http\FormRequest;

class BranchServiceAssignmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'branch_id' => $this->route('branchId'),
        ]);
    }

    public function rules(): array
    {
        return [
            'branch_id'   => ['required', 'integer', 'exists:branches,id'],
            'service_ids' => ['required', 'array', 'min:1'],
            'service_ids.*' => ['integer', 'exists:services,id'],
        ];
    }

    public function branchId(): int
    {
        return $this->input('branch_id');
    }

    public function serviceIds(): array
    {
        return $this->input('service_ids', []);
    }
}
