<?php

namespace App\Infrastructure\Auth;

use App\Domain\User\Entities\User as DomainUser;
use App\Models\Auth\User as EloquentUser;

final class UserMapper
{
    public static function toEloquent(DomainUser $domainUser): EloquentUser
    {
        // If user exists, fetch from DB; otherwise create new
        $eloquentUser = $domainUser->id ? EloquentUser::find($domainUser->id) : new EloquentUser();

        // Map all fields using fillable to allow mass assignment
        $eloquentUser->fill([
            'name' => $domainUser->name,
            'email' => $domainUser->email,
            'password' => $domainUser->password,
            'country' => $domainUser->country,
            'city' => $domainUser->city,
            'title_prefix' => $domainUser->title_prefix,
            'birth_date' => $domainUser->birth_date?->format('Y-m-d'),
            'title_suffix' => $domainUser->title_suffix,
            'phone_number' => $domainUser->phone_number,
            'gender' => $domainUser->gender,
        ]);

        // Persist if it’s a new user (so remember_token works)
        if (!$domainUser->id) {
            $eloquentUser->save();
            $domainUser->id = $eloquentUser->id;
        }

        return $eloquentUser;
    }

    public static function toDomain(EloquentUser $eloquentUser): DomainUser
    {
        return new DomainUser(
            id: $eloquentUser->id,
            name: $eloquentUser->name,
            email: $eloquentUser->email,
            password: $eloquentUser->password,
            country: $eloquentUser->country,
            city: $eloquentUser->city,
            title_prefix: $eloquentUser->title_prefix,
            birth_date: $eloquentUser->birth_date ? new \DateTimeImmutable($eloquentUser->birth_date) : null,
            title_suffix: $eloquentUser->title_suffix,
            phone_number: $eloquentUser->phone_number,
            gender: $eloquentUser->gender,
        );
    }
}
