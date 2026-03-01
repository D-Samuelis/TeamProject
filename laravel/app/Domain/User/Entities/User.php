<?php

namespace App\Domain\User\Entities;

use App\Application\User\DTO\UpdateUserProfileDTO;

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
        public ?string $gender = null
    ) {}

    public function updateProfile(UpdateUserProfileDTO $dto): void {
        $this->name = $dto->name;
        $this->country = $dto->country;
        $this->city = $dto->city;
        $this->title_prefix = $dto->title_prefix;
        $this->title_suffix = $dto->title_suffix;
        $this->birth_date = $dto->birth_date;
        $this->phone_number = $dto->phone_number;
        $this->gender = $dto->gender;
    }
}
