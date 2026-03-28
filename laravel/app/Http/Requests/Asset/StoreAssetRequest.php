<?php

namespace App\Http\Requests\Asset;

use Illuminate\Foundation\Http\FormRequest;
use App\Rules\ServicesBelongToBranches;

class StoreAssetRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $branchIds = $this->input('branch_ids', []);

        return [
            'name'          => 'required|string|max:255',
            'description'   => 'nullable|string',
            'branch_ids'    => 'required_without:service_ids|array',
            'branch_ids.*'  => 'integer|exists:branches,id',
            'service_ids'   => ['required_without:branch_ids', 'array', new ServicesBelongToBranches($branchIds)],
            'service_ids.*' => 'integer|exists:services,id',
        ];
    }

    public function messages(): array
    {
        return [
            'branch_ids.*.exists'  => 'One or more selected branches do not exist.',
            'service_ids.*.exists' => 'One or more selected services do not exist.',
            'branch_ids.required_without'  => 'At least one branch or service must be selected.',
            'service_ids.required_without' => 'At least one branch or service must be selected.',
        ];
    }
}
