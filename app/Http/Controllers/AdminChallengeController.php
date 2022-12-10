<?php

namespace App\Http\Controllers;

use App\Http\Requests\ChallengeManagementRequest;
use App\Http\Requests\QueryRequest;
use App\Models\Challenge;
use App\Services\AdminChallengeService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdminChallengeController extends Controller
{

    private AdminChallengeService $adminChallengeService;

    public function __construct(AdminChallengeService $adminChallengeService)
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
     * @param $admin_challenge
     * @return JsonResponse
     */
    public function show(Request $request, $admin_challenge): JsonResponse
    {
        return $this->controllerResponse($this->adminChallengeService->getByModel($request, $admin_challenge));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param ChallengeManagementRequest $request
     * @param Challenge $admin_challenge
     * @return JsonResponse
     */
    public function update(ChallengeManagementRequest $request, Challenge $admin_challenge): JsonResponse
    {
        return $this->controllerResponse($this->adminChallengeService->update($request, $admin_challenge));
    }
}
