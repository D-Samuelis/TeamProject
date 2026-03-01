<?php

namespace App\Http\Controllers\Web;

use App\Application\User\DTO\LoginUserDTO;
use App\Application\User\DTO\RegisterUserDTO;
use App\Application\User\UseCases\LoginUser;
use App\Application\User\UseCases\LogoutUser;
use App\Application\User\UseCases\RegisterUser;
use App\Http\Controllers\Controller;
use App\Http\Requests\User\LoginRequest;
use App\Http\Requests\User\RegisterRequest;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    /**
     * Show auth page
     */
    public function showAuth(\Illuminate\Http\Request $request)
    {
        $mode = $request->route()->getName();

        return view('pages.auth', [
            'mode' => $mode === 'register' ? 'register' : 'login',
        ]);
    }

    /**
     * Register a new user.
     * Expects 'name', 'email', 'password', and 'password_confirmation' in the request.
     *
     * @return View|RedirectResponse
     */
    public function register(RegisterRequest $request, RegisterUser $registerUser)
    {
        $dto = new RegisterUserDTO(
            $request->input('name'),
            $request->input('email'),
            $request->input('country'),
            $request->input('city'),
            $request->input('password'),

            $request->input('title_prefix'),
            $request->input('birth_date'),
            $request->input('title_suffix'),
            $request->input('phone_number'),
            $request->input('gender'),
        );

        $result = $registerUser->execute($dto);

        Auth::login(\App\Infrastructure\User\UserMapper::toEloquent($result->user));

        return redirect()->route('dashboard')->with('success', 'Welcome!');
    }

    /**
     * Login an existing user.
     * Expects 'email' and 'password' in the request.
     *
     * @return View|RedirectResponse
     */
    public function login(LoginRequest $request, LoginUser $loginUser)
    {
        $dto = new LoginUserDTO(
            $request->input('email'),
            $request->input('password'),
            $request->input('remember', false)
        );

        try {
            $result = $loginUser->execute($dto);

            $eloquentUser = \App\Infrastructure\User\UserMapper::toEloquent($result->user);
            Auth::login($eloquentUser, $dto->remember);

            return redirect()->intended(route('dashboard'))->with('success', 'Welcome back!');
        } catch (\InvalidArgumentException $e) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'email' => [$e->getMessage()],
            ]);
        }
    }

    /**
     * Logout authenticated user
     */
    public function logout(\Illuminate\Http\Request $request, LogoutUser $logoutUser)
    {
        $user = $request->user(); // Eloquent user

        if ($user) {
            // convert to domain or pass id
            $logoutUser->execute($user->id); // LogoutUser will find domain user via repository
        }

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        Auth::logout();

        return redirect()->route('home')->with('success', 'You have been logged out.');
    }
}
