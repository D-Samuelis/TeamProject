<?php

namespace App\Http\Requests\Rule;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRuleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge(['id' => $this->route('ruleId')]);

        if ($this->has('rule_set') && is_string($this->rule_set)) {
            $this->merge(['rule_set' => json_decode($this->rule_set, true)]);
        }
    }

    public function rules(): array
    {
        return [
            'id'          => 'required|integer|exists:rules,id',
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
            'valid_from'  => 'nullable|date',
            'valid_to'    => 'nullable|date|after_or_equal:valid_from',
            'rule_set'             => 'required|array',
            'rule_set.days'        => 'required|array',
            'rule_set.days.*'      => 'array',
            'rule_set.days.*.*.from_time' => 'nullable|date_format:H:i',
            'rule_set.days.*.*.to_time'   => 'nullable|date_format:H:i',
        ];
    }

    public function messages(): array
    {
        return [
            'valid_to.after_or_equal' => 'Valid to must be after or equal to valid from.',
        ];
    }
}
