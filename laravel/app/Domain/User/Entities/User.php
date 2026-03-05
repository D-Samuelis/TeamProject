<?php

namespace App\Domain\User\Entities;

final class User
{
    public function __construct(
        public ?int $id = null,
        public string $name,
        public string $email,
        public string $password,
        public ?string $country = null,
        public ?string $city = null,
        public ?string $title_prefix = null,
        public ?\DateTimeImmutable $birth_date = null,
        public ?string $title_suffix = null,
        public ?string $phone_number = null,
        public ?string $gender = null,
        public ?bool $is_admin = false,
    ) {}

    public function isAdmin(): bool
    {
        return $this->is_admin;
    }
}