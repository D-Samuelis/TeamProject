<?php

namespace App\Application\User\DTO;

class UpdateUserProfileDTO
{
    public function __construct(
        public readonly ?string $name = null,
        public readonly ?string $email = null,
        public readonly ?string $password = null,
        public readonly ?string $city = null,
        public readonly ?string $country = null,
        public readonly ?string $title_prefix = null,
        public readonly ?\DateTimeImmutable $birth_date = null,
        public readonly ?string $title_suffix = null,
        public readonly ?string $phone_number = null,
        public readonly ?string $gender = null,
    ) {}
}
