<?php

namespace App\Application\Auth\DTO;

use App\Http\Requests\Auth\UpdateUserRequest;

class UpdateUserDTO
{
    public function __construct(
        public int $id,
        public ?string $name = null,
        public ?string $email = null,
        public ?string $password = null,
        public ?string $city = null,
        public ?string $country = null,
        public ?string $title_prefix = null,
        public ?\DateTimeImmutable $birth_date = null,
        public ?string $title_suffix = null,
        public ?string $phone_number = null,
        public ?string $gender = null,
    ) {}

    /**
     * Map the request data to the DTO.
     */
    public static function fromRequest(int $userId, UpdateUserRequest $request): self
    {
        $validated = $request->validated();

        return new self(
            id: $userId,
            name: $validated['name'] ?? null,
            email: $validated['email'] ?? null,
            password: $validated['password'] ?? null,
            city: $validated['city'] ?? null,
            country: $validated['country'] ?? null,
            title_prefix: $validated['title_prefix'] ?? null,
            birth_date: isset($validated['birth_date'])
                ? new \DateTimeImmutable($validated['birth_date'])
                : null,
            title_suffix: $validated['title_suffix'] ?? null,
            phone_number: $validated['phone_number'] ?? null,
            gender: $validated['gender'] ?? null,
        );
    }

    /**
     * Convert to array, filtering out null values.
     */
    public function toArray(): array
    {
        return array_filter([
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
        ], fn($value) => !is_null($value));
    }
}
