<?php

namespace App\Http\Controllers\Web;

use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;

use App\Application\Auth\UseCases\RegisterUser;
use App\Application\Auth\UseCases\LoginUser;
use App\Application\Auth\UseCases\LogoutUser;

use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Requests\Auth\LoginRequest;

use App\Application\Auth\DTO\RegisterUserDTO;
use App\Application\Auth\DTO\LoginUserDTO;

class AuthController extends Controller
{
    /**
     * Show auth page
     */
    public function showAuth()
    {
        return view('pages.auth');
    }

    /**
     * Show register form
     */
    public function showRegister()
    {
        return view('pages.auth');
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

        Auth::login($result->user);

        return redirect()->route('dashboard')->with('success', 'Welcome!');
    }

    /**
     * Show login form
     */
    public function showLogin()
    {
        return view('pages.auth');
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
            $result = $loginUser->execute($dto);

            Auth::login($result->user, $dto->remember);

            return redirect()->intended(route('dashboard'))->with('success', 'Welcome back!');
        } catch (\InvalidArgumentException $e) {
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
        $user = $request->user(); // Eloquent user

        if ($user) {
            // convert to domain or pass id
            $logoutUser->execute($user->id); // LogoutUser will find domain user via repository
        }

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        Auth::logout();

        return redirect()->route('/')->with('success', 'You have been logged out.');
    }
}
