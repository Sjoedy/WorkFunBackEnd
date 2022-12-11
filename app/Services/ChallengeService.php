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
    private ChallengeUser $challengeUser;

    /**
     * @param Challenge $challenge
     * @param ChallengeUser $challengeUser
     * @param GroupService $groupService
     * @param ExceptionService $exceptionService
     */
    public function __construct(Challenge        $challenge,
                                ChallengeUser    $challengeUser,
                                GroupService     $groupService,
                                ExceptionService $exceptionService)
    {
        $this->challenge = $challenge;
        $this->groupService = $groupService;
        $this->exceptionService = $exceptionService;
        $this->challengeUser = $challengeUser;
    }

    /**
     * @param $request
     * @return array
     */
    public function all($request): array
    {
        try {
            $user = $request->user('api');
            $groupId = $this->groupService->checkGroupId($request);
            $isGroupAdmin = $this->groupService->isGroupAdmin($user->id, $groupId);
            $challengeUserQuery = $this->challengeUser->query();
            $challengeUserQuery->select(
                'challenge_users.*'
            )
                ->join('challenges', 'challenges.id', 'challenge_users.challenge_id')
                ->with(['user', 'challenge']);
            if ($isGroupAdmin) {
                $challengeUserQuery->where('challenges.group_id', $groupId);
            } else {
                $challengeUserQuery->where('challenge_users.user_id', $user->id);
            }

            $data = $this->formatQuery($request, $challengeUserQuery->orderByDesc('challenge_users.updated_at'), [], ['status']);
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
            $groupId = $this->groupService->checkGroupId($request);
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
            self::challengeAssign($request->users, $challenge, 'save');
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
            $groupId = $this->groupService->checkGroupId($request);
            $data = Challenge::query()->where('group_id', $groupId)->where('challenge_id', $challenge)->first();
            if (!isset($data)) {
                abort(404, __('fail.data_not_found'));
            }
            return $this->serviceResponse(true, __('success.get_data'), 200, $data);
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
            $groupId = $this->groupService->checkGroupId($request);
            $isGroupAdmin = $this->groupService->isGroupAdmin($request->user('api')->id, $groupId);
            if (!$isGroupAdmin) {
                abort(403, __('fail.no_permission'));
            }
            $challenge->title = $request->title;
            $challenge->description = $request->description;
            $challenge->type = $request->type;
            $challenge->point = $request->point;
            $challenge->save();
            self::challengeAssign($request->users, $challenge, 'update');
            return $this->serviceResponse(true, __('success.save_data'), 200, $challenge);
        } catch (Exception $e) {
            $info = $this->exceptionService->getInfo($e);
            return $this->serviceResponse(false, $info['message'], $info['code'], null);
        }
    }

    public function challengeAssign(array $users, $challenge, $action)
    {
        //delete old user in challenge
        if ($action == 'update') ChallengeUser::query()->where('challenge_id', $challenge->id)->whereNotIn('user_id', $users)->delete();
        foreach ($users as $user) {
            $checkOldChallenge = ChallengeUser::query()->where('user_id', $user)->where('challenge_id', $challenge->id)->exists();
            if ($action == 'store') {
                if ($checkOldChallenge) {
                    abort(400, __('fail.user_has_already_assigned_challenge'));
                }
            } else {
                if ($checkOldChallenge) continue;
            }
            $challenge_user = new ChallengeUser();
            $challenge_user->user_id = $user;
            $challenge_user->challenge_id = $challenge->id;
            $challenge_user->save();
        }
    }

    public function updateChallenge($request, $challengeUserId): array
    {
        try {
            $userId = $request->user('api')->id;
            $challengeUser = ChallengeUser::query()->where('id', $challengeUserId)->where('user_id', $userId)->first();
            if (!isset($challengeUser)) {
                abort(404, __('fail.data_not_found'));
            }
            if (in_array($challengeUser->status, ['todo', 're-todo'])) {
                $challengeUser->status = 'doing';
            } elseif ($challengeUser->status == 'doing') {
                $challengeUser->status = 'done';
                $challengeUser->heat_score = $request->score ?? null;
            } else {
                abort(400, __('fail.done_challenge_can_not_update'));
            }
            $challengeUser->save();
            return $this->serviceResponse(true, __('success.update_data'), 200, $challengeUser);
        } catch (Exception $e) {
            $info = $this->exceptionService->getInfo($e);
            return $this->serviceResponse(false, $info['message'], $info['code'], null);
        }
    }
}
