<?php

namespace App\Application\Auth\DTO;

use Illuminate\Http\Request;

class UpdateUserDTO
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

    /**
     * Creates a DTO from a Request object.
     */
    public static function fromRequest(Request $request): self
    {
        return new self(
            name: $request->input('name'),
            email: $request->input('email'),
            password: $request->input('password'),
            city: $request->input('city'),
            country: $request->input('country'),
            title_prefix: $request->input('title_prefix'),
            birth_date: $request->input('birth_date')
                ? new \DateTimeImmutable($request->input('birth_date'))
                : null,
            title_suffix: $request->input('title_suffix'),
            phone_number: $request->input('phone_number'),
            gender: $request->input('gender'),
        );
    }

    /**
     * Converts the DTO to an associative array.
     * Useful for updating Eloquent models or passing to repositories.
     */
    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'email' => $this->email,
            'password' => $this->password,
            'city' => $this->city,
            'country' => $this->country,
            'title_prefix' => $this->title_prefix,
            'birth_date' => $this->birth_date?->format('Y-m-d'),
            'title_suffix' => $this->title_suffix,
            'phone_number' => $this->phone_number,
            'gender' => $this->gender,
        ];
    }

    /**
     * Optional: Returns only the fields that are not null.
     * Often preferred for PATCH/Update requests.
     */
    public function toFilteredArray(): array
    {
        return array_filter($this->toArray(), fn($value) => $value !== null);
    }
}
