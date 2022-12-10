<?php

namespace App\Services;

use App\Models\Challenge;
use App\Models\GroupUser;
use App\Services\Base\BaseService;
use Exception;
use Illuminate\Support\Facades\DB;
use Symfony\Component\ErrorHandler\Exception\FlattenException;

final class ChallengeService extends BaseService
{

    private Challenge $challenge;
    private GroupService $groupService;

    /**
     * @param Challenge $challenge
     * @param GroupService $groupService
     */
    public function __construct(Challenge    $challenge,
                                GroupService $groupService)
    {
        $this->challenge = $challenge;
        $this->groupService = $groupService;
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
            $e = FlattenException::create($e);
            $code = $e->getStatusCode();
            return $this->serviceResponse(false, $e->getMessage(), $code, null);
        }
    }

    /**
     * @param $request
     * @return array
     */
    public function save($request): array
    {
        try {
            $user = $request->user('api');
            $checkGroupId = $this->groupService->userHasGroup($request);
            if (!$checkGroupId['success']) {
                return $this->serviceResponse($checkGroupId['success'], $checkGroupId['message'], $checkGroupId['code'], $checkGroupId['data']);
            }
            $challenge = $this->challenge->newInstance();
            $challenge->title = $request->title;
            $challenge->description = $request->description;
            $challenge->type = $request->type;

            $challenge->save();
            self::addMember($user, $challenge, 'admin');
            DB::commit();
            return $this->serviceResponse(true, __('success.save_data'), 200, $challenge);
        } catch (Exception $e) {
            DB::rollback();
            $e = FlattenException::create($e);
            $code = $e->getStatusCode();
            return $this->serviceResponse(false, $e->getMessage(), $code, null);
        }
    }

    /**
     * @param $challenge
     * @return array
     */
    public function getByModel($challenge): array
    {
        return $this->serviceResponse(true, __('success.get_data'), 200, $challenge);
    }

    /**
     * @param $request
     * @param $challenge
     * @return array
     */
    public function update($request, $challenge): array
    {
        try {
            $isGroupAdmin = self::isGroupAdmin($request->user('api')->id, $challenge->id);
            if (!$isGroupAdmin) {
                abort(403, __('fail.no_permission'));
            }
            $challenge->name = $request->name;
            $challenge->description = $request->description;
            $challenge->save();
            return $this->serviceResponse(true, __('success.save_data'), 200, $challenge);
        } catch (Exception $e) {
            $e = FlattenException::create($e);
            $code = $e->getStatusCode();
            return $this->serviceResponse(false, $e->getMessage(), $code, null);
        }
    }
}
