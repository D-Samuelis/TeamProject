<?php

namespace App\Http\Requests\Asset;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class GetAssetRequest extends FormRequest
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
            'service_id' => $this->route('serviceId'),
            'asset_id' => $this->route('assetId'),
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
            'service_id' => ['required', 'exists:services,id'],
            'asset_id' => [
                'required',
                Rule::exists('asset_service', 'asset_id')->where('service_id', $this->input('service_id')),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'asset_id.exists' => 'This asset is not assigned to the selected service.',
        ];

    }
}
