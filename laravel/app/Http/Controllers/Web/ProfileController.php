<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

use App\Application\User\DTO\UpdateUserProfileDTO;
use App\Application\User\UseCases\UpdateUserProfile;

use App\Http\Requests\User\UpdateRequest;

class ProfileController extends Controller
{
    public function show()
    {
        return view('archive.profile', [
            'user' => Auth::user(),
        ]);
    }

    public function update(UpdateRequest $request, UpdateUserProfile $updateUserProfile)
    {
        $dto = new UpdateUserProfileDTO(
            name: $request->name,
            email: $request->email,
            country: $request->country,
            city: $request->city,
            title_prefix: $request->title_prefix,
            birth_date: $request->birth_date ? new \DateTimeImmutable(
                $request->birth_date
            ) : null,
            title_suffix: $request->title_suffix,
            phone_number: $request->phone_number,
            gender: $request->gender
        );

        $updateUserProfile->execute(Auth::id(), $dto);

        return redirect()->route('profile.show')->with('success', 'Profile updated successfully.');
    }
}
