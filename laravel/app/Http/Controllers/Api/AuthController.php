<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

use App\Application\Auth\RegisterUser;
use App\Application\Auth\LoginUser;
use App\Application\Auth\LogoutUser;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\LoginRequest;
use App\Application\Auth\DTO\RegisterUserDTO;
use App\Application\Auth\DTO\LoginUserDTO;


class AuthController extends Controller
{
    /**
     * Register a new user.
     * Expects 'name', 'email', 'password', and 'password_confirmation' in the request.
     * 
     * @return JsonResponse
     */
    public function register(RegisterRequest $request, RegisterUser $registerUser): JsonResponse
    {
        $dto = new RegisterUserDTO(
            $request->input('name'),
            $request->input('email'),
            $request->input('password')
        );

        $result = $registerUser->execute($dto);

        return response()->json([
            'user' => [
                'id' => $result->user->getId(),
                'name' => $result->user->name,
                'email' => $result->user->email,
            ],
            'token' => $result->token
        ]);
    }


    /**
     * Login an existing user.
     * Expects 'email' and 'password' in the request.
     * 
     * @return JsonResponse
     */
    public function login(LoginRequest $request, LoginUser $loginUser): JsonResponse
    {
        $dto = new LoginUserDTO(
            $request->input('email'),
            $request->input('password'),
            (bool)$request->input('remember', false)
        );

        try {
            $result = $loginUser->execute($dto);
            $u = $result->user;

            return response()->json([
                'user' => [
                    'id' => $u->getId(),
                    'name' => $u->name,
                    'email' => $u->email,
                ],
                'token' => $result->token
            ]);
        } catch (\InvalidArgumentException $e) {
            return response()->json(['message' => $e->getMessage()], 401);
        }
    }

    /**
     * Logout the authenticated user.
     *  
     * @return JsonResponse
     */
    public function logout(Request $request, LogoutUser $logoutUser): JsonResponse
    {
        // For token-based logout, you may want to revoke only current token or all tokens.
        // If using Sanctum and the request uses token auth, you can revoke the current token:
        $user = $request->user();
        if ($user) {
            $logoutUser->execute($user->id);
        }

        return response()->json(['message' => 'Logged out']);
    }
}
