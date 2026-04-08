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
        $branchId = $this->input('branch_id'); 

        return [
            'name'          => 'required|string|max:255',
            'description'   => 'nullable|string',
            'branch_id'     => 'required|integer|exists:branches,id',
            'service_ids'   => ['required', 'array', 'min:1', new ServicesBelongToBranches([$branchId])],
            'service_ids.*' => 'integer|exists:services,id',
        ];
    }

    public function messages(): array
    {
        return [
            'branch_id.required'   => 'You must select a branch for this asset.',
            'branch_id.exists'     => 'The selected branch does not exist.',
            'service_ids.required' => 'At least one service must be assigned to this asset.',
            'service_ids.min'      => 'You must select at least one service.',
            'service_ids.*.exists' => 'One or more selected services do not exist.',
        ];
    }
}
