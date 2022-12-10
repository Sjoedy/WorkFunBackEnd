<?php

namespace App\Services;

use App\Models\Challenge;
use App\Models\ChallengeUser;
use App\Models\GroupUser;
use App\Services\Base\BaseService;
use Exception;
use Illuminate\Support\Facades\DB;
use Symfony\Component\ErrorHandler\Exception\FlattenException;

final class ChallengeService extends BaseService
{

    private Challenge $challenge;
    private GroupService $groupService;
    private ExceptionService $exceptionService;

    /**
     * @param Challenge $challenge
     * @param GroupService $groupService
     * @param ExceptionService $exceptionService
     */
    public function __construct(Challenge        $challenge,
                                GroupService     $groupService,
                                ExceptionService $exceptionService)
    {
        $this->challenge = $challenge;
        $this->groupService = $groupService;
        $this->exceptionService = $exceptionService;
    }

    /**
     * @param $request
     * @return array
     */
    public function all($request): array
    {
        try {
            $challengeQuery = $this->challenge->query();
            $data = $this->formatQuery($request, $challengeQuery);
            return $this->serviceResponse(true, __('success.get_data'), 200, $data);
        } catch (Exception $e) {
            $info = $this->exceptionService->getInfo($e);
            return $this->serviceResponse(false, $info['message'], $info['code'], null);
        }
    }

    /**
     * @param $request
     * @return array
     */
    public function save($request): array
    {
        DB::beginTransaction();
        try {
            $user = $request->user('api');
            $checkGroupId = $this->groupService->userHasGroup($request);
            if (!$checkGroupId['success']) {
                abort(404, ($checkGroupId['message']));
            }
            $groupId = $checkGroupId['data']['group_id'];
            $isGroupAdmin = $this->groupService->isGroupAdmin($user->id, $groupId);
            if (!$isGroupAdmin) {
                abort(403, __('fail.no_permission'));
            }
            $challenge = $this->challenge->newInstance();
            $challenge->title = $request->title;
            $challenge->description = $request->description;
            $challenge->type = $request->type;
            $challenge->group_id = $groupId;
            $challenge->point = $request->point;
            $challenge->save();
            self::challengeAssign($request->users, $challenge);
            DB::commit();
            return $this->serviceResponse(true, __('success.save_data'), 200, $challenge);
        } catch (Exception $e) {
            DB::rollback();
            $info = $this->exceptionService->getInfo($e);
            return $this->serviceResponse(false, $info['message'], $info['code'], null);
        }
    }

    /**
     * @param $request
     * @param $challenge
     * @return array
     */
    public function getByModel($request, $challenge): array
    {
        try {
            $checkGroupId = $this->groupService->userHasGroup($request);
            if (!$checkGroupId['success']) {
                abort(404, $checkGroupId['message']);
            }
            $groupId = $checkGroupId['data']['group_id'];
            $challenge = Challenge::query()->where('group_id', $groupId)->where('challenge_id', $challenge)->first();
            if (!isset($challenge)) {
                abort(404, __('fail.data_not_found'));
            }
            return $this->serviceResponse(true, __('success.get_data'), 200, $challenge);
        } catch (Exception $e) {
            $info = $this->exceptionService->getInfo($e);
            return $this->serviceResponse(false, $info['message'], $info['code'], null);
        }
    }

    /**
     * @param $request
     * @param $challenge
     * @return array
     */
    public function update($request, $challenge): array
    {
        try {
            $isGroupAdmin = $this->groupService->isGroupAdmin($request->user('api')->id, $challenge->id);
            if (!$isGroupAdmin) {
                abort(403, __('fail.no_permission'));
            }
            $challenge->title = $request->title;
            $challenge->description = $request->description;
            $challenge->type = $request->type;
            $challenge->point = $request->point;
            $challenge->save();
            self::challengeAssign($request->users, $challenge);
            return $this->serviceResponse(true, __('success.save_data'), 200, $challenge);
        } catch (Exception $e) {
            $info = $this->exceptionService->getInfo($e);
            return $this->serviceResponse(false, $info['message'], $info['code'], null);
        }
    }

    public function challengeAssign(array $users, $challenge)
    {
        foreach ($users as $user) {
            $checkOldChallenge = ChallengeUser::query()->where('user_id', $user)->where('challenge_id', $challenge->id)->exists();
            if ($checkOldChallenge) {
                abort(400, __('fail.user_has_already_assigned_challenge'));
            }
            $challenge_user = new ChallengeUser();
            $challenge_user->user_id = $user;
            $challenge_user->challenge_id = $challenge->id;
            $challenge_user->save();
        }
    }
}
