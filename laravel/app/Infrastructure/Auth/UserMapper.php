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

        // Map all fields
        $eloquentUser->name = $domainUser->name;
        $eloquentUser->email = $domainUser->email;
        $eloquentUser->password = $domainUser->password;
        $eloquentUser->country = $domainUser->country;
        $eloquentUser->city = $domainUser->city;
        $eloquentUser->title_prefix = $domainUser->title_prefix;
        $eloquentUser->birth_date = $domainUser->birth_date?->format('Y-m-d');
        $eloquentUser->title_suffix = $domainUser->title_suffix;
        $eloquentUser->phone_number = $domainUser->phone_number;
        $eloquentUser->gender = $domainUser->gender;

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