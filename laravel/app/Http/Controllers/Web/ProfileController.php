<?php

namespace App\Http\Controllers\Web;

use App\Application\Auth\DTO\UpdateUserDTO;
use App\Application\Auth\UseCases\UpdateUser;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\UpdateUserRequest;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    /**
     * Show the user profile edit form.
     */
    public function show()
    {
        return view('web.customer.profile.show', [
            'user' => Auth::user(),
            'count' => 0
        ]);
    }

    /**
     * Update the user profile.
     */
    public function update(UpdateUserRequest $request, UpdateUser $updateUser)
    {
        $updateUser->execute(Auth::id(), UpdateUserDTO::fromRequest($request));
        return back()->with('success', 'Profile updated successfully!');
    }
}
