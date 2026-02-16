<?php
// app/Infrastructure/Persistence/EloquentUserRepository.php
namespace App\Infrastructure\Persistence;

use App\Domain\User\Entities\User as DomainUser;
use App\Domain\User\Repositories\UserRepository;
use App\Models\User as EloquentUser;
use DateTimeImmutable;

// This is the translation boundary. All Eloquent specifics (roles, tokens) can be handled here or via dedicated infrastructure services.
final class EloquentUserRepository implements UserRepository
{
    public function save(DomainUser $user): DomainUser
    {
        $eloquent = $user->getId()
            ? EloquentUser::find($user->getId())
            : new EloquentUser();

        $eloquent->name = $user->name;
        $eloquent->email = $user->email;
        $eloquent->password = $user->passwordHash;
        $eloquent->save();

        return $this->toDomain($eloquent);
    }

    public function findById(string $id): ?DomainUser
    {
        $u = EloquentUser::find($id);
        return $u ? $this->toDomain($u) : null;
    }

    public function findByEmail(string $email): ?DomainUser
    {
        $u = EloquentUser::where('email', $email)->first();
        return $u ? $this->toDomain($u) : null;
    }

    private function toDomain(EloquentUser $e): DomainUser
    {
        return new DomainUser(
            $e->id,
            $e->name,
            $e->email,
            $e->password,
            $e->created_at ? new DateTimeImmutable($e->created_at) : null
        );
    }
}
