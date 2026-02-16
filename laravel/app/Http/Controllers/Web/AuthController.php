<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\RegisterRequest;
use App\Application\Auth\RegisterUser;
use App\Application\Auth\DTO\RegisterUserDTO;
use App\Http\Requests\LoginRequest;
use App\Application\Auth\LoginUser;
use App\Application\Auth\LogoutUser;
use App\Application\Auth\DTO\LoginUserDTO;

class AuthController extends Controller
{
    /**
     * Show register form
     */
    public function showRegister()
    {
        return view('auth.register');
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
            $request->input('password')
        );

        $result = $registerUser->execute($dto);

        // log the user in (Eloquent model needed)
        $eloquentUser = \App\Models\User::find($result->user->getId());
        Auth::login($eloquentUser);

        return redirect()->route('dashboard')->with('success', 'Welcome!');
    }

    /**
     * Show login form
     */
    public function showLogin()
    {
        return view('auth.login');
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
            (bool)$request->input('remember', false)
        );

        try {
            $result = $loginUser->execute($dto); // returns RegisteredUserDTO
            $domainUser = $result->user;

            // convert domain user -> Eloquent model for Auth::login
            $eloquent = \App\Models\User::find($domainUser->getId());
            if (!$eloquent) {
                // This should rarely happen; repository and Eloquent are consistent.
                throw new \RuntimeException('Eloquent user not found.');
            }

            Auth::login($eloquent, $dto->remember);

            // Optionally: store token for API usage, or return it for SPA
            return redirect()->intended(route('dashboard'))->with('success', 'Welcome back!');
        } catch (\InvalidArgumentException $e) {
            // map domain/auth failure to form error
            throw \Illuminate\Validation\ValidationException::withMessages([
                'email' => [$e->getMessage()],
            ]);
        }
    }

    /**
     * Logout authenticated user (WEB)
     */
    public function logout(\Illuminate\Http\Request $request, LogoutUser $logoutUser)
    {
        $eloquent = $request->user(); // Eloquent user

        if ($eloquent) {
            // convert to domain or pass id
            $logoutUser->execute($eloquent->id); // LogoutUser will find domain user via repository
        }

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        Auth::logout();

        return redirect()->route('login')->with('success', 'You have been logged out.');
    }
}
