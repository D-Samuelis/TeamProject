<?php

namespace App\Application\Service\Services;

class DurationParser
{
    /**
     * Parse a human-readable duration string into total minutes.
     * Supports: 2w, 1d, 3h, 5m (case-insensitive, any order)
     * Examples: "2d 3h", "1w", "90m", "1d 12h 30m"
     */
    public static function toMinutes(?string $input): ?int
    {
        if (blank($input)) return null;

        $units = ['w' => 10080, 'd' => 1440, 'h' => 60, 'm' => 1];
        $total = 0;
        $matched = false;

        foreach ($units as $unit => $multiplier) {
            if (preg_match('/(\d+)\s*' . $unit . '/i', $input, $matches)) {
                $total += (int) $matches[1] * $multiplier;
                $matched = true;
            }
        }

        return $matched ? $total : null;
    }

    /**
     * Convert minutes back to a human-readable string.
     * Useful for displaying the value back to the owner.
     */
    public static function fromMinutes(?int $minutes): ?string
    {
        if (is_null($minutes)) return null;

        $parts = [];
        $units = ['w' => 10080, 'd' => 1440, 'h' => 60, 'm' => 1];

        foreach ($units as $unit => $value) {
            if ($minutes >= $value) {
                $parts[] = intdiv($minutes, $value) . $unit;
                $minutes %= $value;
            }
        }

        return implode(' ', $parts);
    }
}
