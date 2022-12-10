<?php

namespace App\Http\Controllers;

use App\Http\Requests\ChallengeManagementRequest;
use App\Http\Requests\QueryRequest;
use App\Services\ChallengeService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ChallengeController extends Controller
{

    private ChallengeService $challengeService;

    public function __construct(ChallengeService $challengeService)
    {
        $this->challengeService = $challengeService;
    }

    /**
     * Display a listing of the resource.
     *
     * @param QueryRequest $request
     * @return JsonResponse
     */
    public function index(QueryRequest $request): JsonResponse
    {
        return $this->controllerResponse($this->challengeService->all($request));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param ChallengeManagementRequest $request
     * @return JsonResponse
     */
    public function store(ChallengeManagementRequest $request): JsonResponse
    {
        return $this->controllerResponse($this->challengeService->save($request));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
