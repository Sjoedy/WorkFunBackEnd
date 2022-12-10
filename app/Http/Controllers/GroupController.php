<?php

namespace App\Http\Controllers;

use App\Http\Requests\JoinGroupRequest;
use App\Http\Requests\GroupManagementRequest;
use App\Http\Requests\QueryRequest;
use App\Models\Group;
use App\Services\GroupService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class GroupController extends Controller
{
    private GroupService $groupService;

    /**
     * @param GroupService $groupService
     */
    public function __construct(GroupService $groupService)
    {
        $this->groupService = $groupService;
    }

    /**
     * Display a listing of Group.
     *
     * @param QueryRequest $request
     * @return JsonResponse
     */
    public function index(QueryRequest $request): JsonResponse
    {
        return $this->controllerResponse($this->groupService->all($request));
    }

    /**
     * Store a newly created group.
     *
     * @param GroupManagementRequest $request
     * @return JsonResponse
     */
    public function store(GroupManagementRequest $request): JsonResponse
    {
        return $this->controllerResponse($this->groupService->save($request));
    }

    /**
     * Display the specified group.
     *
     * @param Group $group
     * @return JsonResponse
     */
    public function show(Group $group): JsonResponse
    {
        return $this->controllerResponse($this->groupService->getByModel($group));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param GroupManagementRequest $request
     * @param Group $group
     * @return JsonResponse
     */
    public function update(GroupManagementRequest $request, Group $group): JsonResponse
    {
        return $this->controllerResponse($this->groupService->update($request, $group));
    }

    /**
     * @param JoinGroupRequest $request
     * @return JsonResponse
     */
    public function joinGroup(JoinGroupRequest $request): JsonResponse
    {
        return $this->controllerResponse($this->groupService->joinGroup($request));
    }

    /**
     * @param QueryRequest $request
     * @param $groupId
     * @return JsonResponse
     */
    public function groupInfo(QueryRequest $request, $groupId): JsonResponse
    {
        return $this->controllerResponse($this->groupService->groupInfo($request, $groupId));
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function userHasGroup(Request $request): JsonResponse
    {
        return $this->controllerResponse($this->groupService->userHasGroup($request));
    }
}
