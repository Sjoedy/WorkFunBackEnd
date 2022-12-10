<?php

namespace App\Services;

use App\Models\Challenge;
use App\Models\ChallengeUser;
use App\Models\GroupUser;
use App\Services\Base\BaseService;
use Exception;
use Illuminate\Support\Facades\DB;
use Symfony\Component\ErrorHandler\Exception\FlattenException;

final class UserChallengeService extends BaseService
{

    private Challenge $challenge;
    private GroupService $groupService;
    private ExceptionService $exceptionService;
    private UserChallengeService $userChallengeService;

    /**
     * @param ExceptionService $exceptionService
     */
    public function __construct(ExceptionService $exceptionService)
    {
        $this->exceptionService = $exceptionService;
    }

    /**
     * @param $request
     * @param $challengeUserId
     * @return array
     */
    public function update($request, $challengeUserId): array
    {
        try {
            $userId = $request->user('api')->id;
            $challengeUser = ChallengeUser::query()->where('id', $challengeUserId)->where('user_id', $userId)->first();
            if (!isset($challengeUser)) {
                abort(404, __('fail.data_not_found'));
            }
            if (in_array($challengeUser->status, ['todo', 're-todo'])) {
                $challengeUser->status = 'doing';
            } elseif ($challengeUser->status == 'doing')
                $challengeUser->status = 'done';
            else {
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
