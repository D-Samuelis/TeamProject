<?php
// app/Domain/User/Entities/User.php
namespace App\Domain\User\Entities;

use DateTimeImmutable;

// Purpose: plain PHP domain entity with domain rules. No Laravel here.
final class User
{
    public function __construct(
        public ?string $id,
        public string $name,
        public string $email,
        public string $passwordHash,
        public ?DateTimeImmutable $createdAt = null
    ) {
        $this->createdAt = $createdAt ?? new DateTimeImmutable();
    }

    public function changeName(string $name): void
    {
        if (empty($name)) {
            throw new \InvalidArgumentException('Name cannot be empty.');
        }
        $this->name = $name;
    }

    public function getId(): ?string
    {
        return $this->id;
    }
}
