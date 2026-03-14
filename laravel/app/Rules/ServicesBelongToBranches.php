<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use App\Models\Business\Service;

class ServicesBelongToBranches implements ValidationRule
{
    public function __construct(private readonly array $branchIds) {}

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (empty($this->branchIds) || empty($value)) {
            return;
        }

        $invalidServices = Service::whereIn('id', $value)
            ->whereDoesntHave('branches', fn($q) => $q->whereIn('branches.id', $this->branchIds))
            ->pluck('name');

        if ($invalidServices->isNotEmpty()) {
            $names = $invalidServices->join(', ');
            $fail("The following services are not available in any of the selected branches: {$names}.");
        }
    }
}
