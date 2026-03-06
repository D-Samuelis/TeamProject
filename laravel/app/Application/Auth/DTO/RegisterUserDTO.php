<?php

namespace App\Application\Auth\DTO;

final class RegisterUserDTO
{
    public function __construct(
        public string $name,
        public string $email,
        public string $country,
        public string $city,
        public string $password,
        public ?string $title_prefix,
        public ?string $birth_date,
        public ?string $title_suffix,
        public ?string $phone_number,
        public ?string $gender
    ) {}

    public function toArray(): array
    {
        return [
            'name'         => $this->name,
            'email'        => $this->email,
            'country'      => $this->country,
            'city'         => $this->city,
            'password'     => $this->password,
            'title_prefix' => $this->title_prefix,
            'birth_date'   => $this->birth_date,
            'title_suffix' => $this->title_suffix,
            'phone_number' => $this->phone_number,
            'gender'       => $this->gender,
        ];
    }
}
