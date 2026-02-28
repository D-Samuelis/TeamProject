<?php
// app/Application/Auth/DTO/RegisterUserDTO.php
namespace App\Application\Auth\DTO;

// Purpose: typed data transfer objects that Application layer uses as input/output.
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
}
