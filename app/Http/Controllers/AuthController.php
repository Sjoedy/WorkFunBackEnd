<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthController extends Controller
{

    private AuthService $authService;

    /**
     * @param AuthService $authService
     */
    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function register(RegisterRequest $request): JsonResponse
    {
        return $this->returnController($this->authService->register($request));
    }

    public function login(LoginRequest $request): JsonResponse
    {
        return $this->returnController($this->authService->login($request));
    }

    public function logout(): JsonResponse
    {
        return $this->returnController($this->authService->logout());
    }
}
