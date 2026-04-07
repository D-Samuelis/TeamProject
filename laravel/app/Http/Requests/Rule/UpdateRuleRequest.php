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
            'rule_set'          => 'required|array',
            'rule_set.days'     => [
                'required',
                'array',
                function ($attribute, $days, $fail) {
                    foreach ($days as $dayIndex => $ranges) {
                        if (empty($ranges)) continue;

                        $accepted = [];

                        foreach ($ranges as $rangeIndex => $range) {
                            $from = $range['from_time'] ?? null;
                            $to   = $range['to_time']   ?? null;

                            if (!$from || !$to) continue;

                            [$fh, $fm] = explode(':', $from);
                            [$th, $tm] = explode(':', $to);
                            $fromMin = (int)$fh * 60 + (int)$fm;
                            $toMin   = (int)$th * 60 + (int)$tm;

                            if ($fromMin >= $toMin) {
                                $fail("Day {$dayIndex}, range " . ($rangeIndex + 1) . ": start time ({$from}) must be before end time ({$to}).");
                                continue;
                            }

                            foreach ($accepted as $a) {
                                if ($fromMin < $a['toMin'] && $toMin > $a['fromMin']) {
                                    $fail("Day {$dayIndex}, range " . ($rangeIndex + 1) . ": {$from}–{$to} overlaps with {$a['from']}–{$a['to']}.");
                                    continue 2;
                                }
                            }

                            $accepted[] = ['fromMin' => $fromMin, 'toMin' => $toMin, 'from' => $from, 'to' => $to];
                        }
                    }
                },
            ],
            'rule_set.days.*'             => 'array',
            'rule_set.days.*.*.from_time' => 'nullable|date_format:H:i',
            'rule_set.days.*.*.to_time'   => [
                'nullable',
                'date_format:H:i',
                function ($attribute, $value, $fail) {
                    preg_match('/rule_set\.days\.(\w+)\.(\d+)\.to_time/', $attribute, $matches);
                    if (!$matches || !$value) return;
                    [, $day, $index] = $matches;
                    $fromTime = $this->input("rule_set.days.{$day}.{$index}.from_time");
                    if ($fromTime && $value <= $fromTime) {
                        $fail("The closing time must be after the opening time ({$fromTime}).");
                    }
                }
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'valid_to.after_or_equal'  => 'Valid to must be after or equal to valid from.',
            'rule_set.days.required'   => 'The schedule must include day definitions.',
            'valid_from.date'          => 'Valid from must be a valid date.',
            'valid_to.date'            => 'Valid to must be a valid date.',
            'rule_set.days.*.*.from_time.date_format' => 'From time must be in HH:MM format.',
            'rule_set.days.*.*.to_time.date_format'   => 'To time must be in HH:MM format.',
            'rule_set.days.*.*.to_time.after_or_equal'   => 'Time to must be after or equal to Time from.',
        ];
    }
}
