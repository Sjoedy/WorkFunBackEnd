<?php

namespace App\Http\Controllers;

use App\Http\Requests\ChallengeManagementRequest;
use App\Http\Requests\QueryRequest;
use App\Models\Challenge;
use App\Services\ChallengeService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ChallengeController extends Controller
{

    private ChallengeService $adminChallengeService;

    public function __construct(ChallengeService $adminChallengeService)
    {
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
     * Store a newly created resource in storage.
     *
     * @param ChallengeManagementRequest $request
     * @return JsonResponse
     */
    public function store(ChallengeManagementRequest $request): JsonResponse
    {
        return $this->controllerResponse($this->adminChallengeService->save($request));
    }

    /**
     * Display the specified resource.
     *
     * @param Request $request
     * @param $challenge
     * @return JsonResponse
     */
    public function show(Request $request, $challenge): JsonResponse
    {
        return $this->controllerResponse($this->adminChallengeService->getByModel($request, $challenge));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param ChallengeManagementRequest $request
     * @param Challenge $challenge
     * @return JsonResponse
     */
    public function update(ChallengeManagementRequest $request, Challenge $challenge): JsonResponse
    {
        return $this->controllerResponse($this->adminChallengeService->update($request, $challenge));
    }

    public function updateChallenge(Request $request, $id): JsonResponse
    {
        return $this->controllerResponse($this->adminChallengeService->updateChallenge($request, $id));
    }
}
