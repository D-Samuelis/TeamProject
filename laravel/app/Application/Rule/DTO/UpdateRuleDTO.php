<?php

namespace App\Application\Rule\DTO;

class UpdateRuleDTO
{
    public function __construct(
        public int     $id,
        public string  $title,
        public ?string $description = null,
        public ?string $valid_from  = null,
        public ?string $valid_to    = null,
        public ?array  $rule_set    = [],
    ) {}

    public function toArray(): array
    {
        return [
            'title'       => $this->title,
            'description' => $this->description,
            'valid_from'  => $this->valid_from,
            'valid_to'    => $this->valid_to,
            'rule_set'    => $this->rule_set,
        ];
    }
}
