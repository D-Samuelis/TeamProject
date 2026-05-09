<?php

namespace App\Application\DTO;

class UserSearchDTO
{
    public function __construct(
        public readonly ?string $userName = null,
        public readonly ?string $userEmail = null,
        public readonly ?string $phoneNumber = null,
        public readonly ?string $isAdmin = null,
        public readonly ?string $country = null,
        public readonly ?string $city = null,
        public readonly ?string $gender = null,
        public readonly ?string $notificationType = null,
        public readonly int $perPage = 15,
        public readonly int $page = 1,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            userName: $data['user_name'] ?? null,
            userEmail: $data['user_email'] ?? null,
            phoneNumber: $data['phone_number'] ?? null,
            isAdmin: $data['is_admin'] ?? null,
            country: $data['country'] ?? null,
            city: $data['city'] ?? null,
            gender: $data['gender'] ?? null,
            notificationType: $data['notification_type'] ?? null,
            perPage: isset($data['per_page']) ? (int) $data['per_page'] : 15,
            page: isset($data['page']) ? (int) $data['page'] : 1,
        );
    }
}
