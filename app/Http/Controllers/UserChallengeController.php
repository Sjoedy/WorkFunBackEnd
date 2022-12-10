<?php

namespace App\Http\Controllers;

use App\Http\Requests\QueryRequest;
use App\Services\AdminChallengeService;
use App\Services\UserChallengeService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserChallengeController extends Controller
{
    private UserChallengeService $userChallengeService;
    private AdminChallengeService $adminChallengeService;

    public function __construct(UserChallengeService  $userChallengeService,
                                AdminChallengeService $adminChallengeService)
    {
        $this->userChallengeService = $userChallengeService;
        $this->adminChallengeService = $adminChallengeService;
    }


    /**
     * Display a listing of the resource.
     *
     * @param QueryRequest $request
     * @return JsonResponse
     */
    public function index(QueryRequest $request): JsonResponse
    {
        return $this->controllerResponse($this->adminChallengeService->all($request));
    }

    /**
     * Display the specified resource.
     *
     * @param Request $request
     * @param $user_challenge
     * @return JsonResponse
     */
    public function show(Request $request, $user_challenge): JsonResponse
    {
        return $this->controllerResponse($this->adminChallengeService->getByModel($request, $user_challenge));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param $user_challenge
     * @return JsonResponse
     */
    public function update(Request $request, $user_challenge): JsonResponse
    {
        return $this->controllerResponse($this->userChallengeService->update($request, $user_challenge));
    }
}
