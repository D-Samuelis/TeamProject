<?php

namespace App\Http\Requests\Business;

use Illuminate\Foundation\Http\FormRequest;

class SearchBusinessRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'q'              => 'nullable|string|max:100',
            'types'          => 'nullable|array',
            'types.*'        => 'string|in:business,branch,service',
            'city'           => 'nullable|string|max:50',
            'max_price'      => 'nullable|numeric|min:0',
            'max_duration'   => 'nullable|integer|min:1',
            'location_types' => 'nullable|array',
            'location_types.*' => 'string|in:branch,online,client_address,hybrid',
        ];
    }
}
