<?php

namespace App\Http\Requests\Asset;

use Illuminate\Foundation\Http\FormRequest;
use App\Rules\ServicesBelongToBranches;

class UpdateAssetRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'id' => $this->route('assetId'),
        ]);
    }

    public function rules(): array
    {
        $branchIds = $this->input('branch_ids', []);

        return [
            'id'            => 'required|integer|exists:assets,id',
            'name'          => 'required|string|max:255',
            'description'   => 'nullable|string',
            'branch_ids'    => 'nullable|array',
            'branch_ids.*'  => 'integer|exists:branches,id',
            'service_ids'   => ['nullable', 'array', new ServicesBelongToBranches($branchIds)],
            'service_ids.*' => 'integer|exists:services,id',
        ];
    }

    public function messages(): array
    {
        return [
            'branch_ids.*.exists'  => 'One or more selected branches do not exist.',
            'service_ids.*.exists' => 'One or more selected services do not exist.',
        ];
    }
}
