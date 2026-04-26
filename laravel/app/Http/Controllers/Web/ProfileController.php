<?php

namespace App\Http\Controllers\Web;

use App\Application\Auth\DTO\UpdateUserDTO;
use App\Application\Auth\DTO\UpdateUserSettingsDTO;
use App\Application\Auth\UseCases\DeleteUser;
use App\Application\Auth\UseCases\UpdateUser;
use App\Application\Auth\UseCases\UpdateUserSettings;
use App\Exceptions\Auth\CannotDeleteAccountException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\DestroyProfileRequest;
use App\Http\Requests\Auth\UpdateUserRequest;
use App\Http\Requests\Auth\UpdateUserSettingsRequest;
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
            'count' => 0,
        ]);
    }

    /**
     * Update the user profile.
     */
    public function update(UpdateUserRequest $request, UpdateUser $updateUser)
    {
        $updateUser->execute(Auth::id(), UpdateUserDTO::fromRequest($request));
        return redirect(route('profile.show') . '#personal')->with('success', 'Profile updated successfully!');
    }

    public function updateSettings(UpdateUserSettingsRequest $request, UpdateUserSettings $updateSettings)
    {
        $updateSettings->execute(Auth::id(), UpdateUserSettingsDTO::fromRequest($request));
        return redirect(route('profile.show') . '#settings')->with('success', 'Settings updated successfully!');
    }

    /**
     * Hard delete the user account.
     */
    public function destroy(DestroyProfileRequest $request, DeleteUser $deleteUser)
    {
        try {
            $deleteUser->execute($request->user());
        } catch (CannotDeleteAccountException $e) {
            return redirect()->to(route('profile.show') . '#settings')->with('error', $e->getMessage());
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home')->with('success', 'Your account has been permanently deleted.');
    }
}
