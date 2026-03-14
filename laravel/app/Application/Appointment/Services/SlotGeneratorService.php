<?php

namespace App\Application\Appointment\Services;

use Carbon\Carbon;
use App\Models\Business\Asset;
use App\Domain\Appointment\Interfaces\AppointmentRepositoryInterface;

class SlotGeneratorService
{
    public function __construct(
        private readonly AppointmentRepositoryInterface $appointmentRepo,
    ) {}

    public function generate(
        Asset $asset,
        Carbon $date,
        int $durationMinutes,
        int $bufferMinutes = 0
    ): array {
        // ISO day: Mon=1..Sun=7, minus 1 gives Mon=0..Sun=6
        $dayOfWeek = $date->dayOfWeekIso - 1;

        $ranges = $this->getRangesForDate($asset, $date, $dayOfWeek);

        if (empty($ranges)) {
            return [];
        }

        $allSlots   = $this->buildSlots($ranges, $durationMinutes, $bufferMinutes);
        $takenSlots = $this->appointmentRepo->getTakenSlots($asset->id, $date)->toArray();

        return array_values(array_filter(
            $allSlots,
            fn(string $slot) => ! in_array($slot, $takenSlots, true)
        ));
    }

    private function getRangesForDate(Asset $asset, Carbon $date, int $dayOfWeek): array
    {
        $ranges = [];

        foreach ($asset->rules as $rule) {
            $validFrom = Carbon::parse($rule->valid_from)->startOfDay();
            $validTo   = Carbon::parse($rule->valid_to)->endOfDay();

            if (! $date->between($validFrom, $validTo)) {
                continue;
            }

            $ruleSet = is_string($rule->rule_set)
                ? json_decode($rule->rule_set, true)
                : $rule->rule_set;

            if (isset($ruleSet['days'])) {
                $ruleSet = $ruleSet['days'];
            }

            $dayKey = (string) $dayOfWeek;

            if (empty($ruleSet[$dayKey])) {
                continue;
            }

            foreach ($ruleSet[$dayKey] as $range) {
                $ranges[] = [
                    'from' => $range['from_time'],
                    'to'   => $range['to_time'],
                ];
            }
        }

        return $ranges;
    }

    private function buildSlots(array $ranges, int $duration, int $buffer): array
    {
        $step  = $duration + $buffer;
        $slots = [];

        foreach ($ranges as $range) {
            $start = $this->timeToMinutes($range['from']);
            $end   = $this->timeToMinutes($range['to']);
            $cur   = $start;

            while ($cur + $duration <= $end) {
                $slots[] = $this->minutesToTime($cur);
                $cur += $step;
            }
        }

        return $slots;
    }

    private function timeToMinutes(string $time): int
    {
        [$h, $m] = explode(':', $time);
        return ((int) $h) * 60 + (int) $m;
    }

    private function minutesToTime(int $minutes): string
    {
        return sprintf('%02d:%02d', intdiv($minutes, 60), $minutes % 60);
    }
}
