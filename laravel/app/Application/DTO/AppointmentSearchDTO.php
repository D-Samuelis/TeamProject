<?php

namespace App\Application\DTO;

class AppointmentSearchDTO
{
    public function __construct(
        public readonly ?string $dateFrom    = null,
        public readonly ?string $dateTo      = null,
        public readonly ?string $timeFrom    = null,
        public readonly ?string $timeTo      = null,
        public readonly array   $statuses    = [],
        public readonly ?string $serviceName = null,
        public readonly ?float  $priceMin    = null,
        public readonly ?float  $priceMax    = null,
        public readonly ?int    $durationMin = null,
        public readonly ?int    $durationMax = null,
        public readonly ?int    $userId      = null,
        public readonly int     $perPage     = 15,
        public readonly int     $page        = 1,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            dateFrom:    $data['date_from']    ?? null,
            dateTo:      $data['date_to']      ?? null,
            timeFrom:    $data['time_from']    ?? null,
            timeTo:      $data['time_to']      ?? null,
            statuses:    $data['statuses']     ?? [],
            serviceName: $data['service_name'] ?? null,
            priceMin:    isset($data['price_min'])    ? (float) $data['price_min']    : null,
            priceMax:    isset($data['price_max'])    ? (float) $data['price_max']    : null,
            durationMin: isset($data['duration_min']) ? (int)   $data['duration_min'] : null,
            durationMax: isset($data['duration_max']) ? (int)   $data['duration_max'] : null,
            userId:      isset($data['user_id'])      ? (int)   $data['user_id']      : null,
            perPage:     isset($data['per_page'])     ? (int)   $data['per_page']     : 15,
            page:        isset($data['page'])         ? (int)   $data['page']         : 1,
        );
    }
}
