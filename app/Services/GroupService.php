<?php

namespace App\Services;

use App\Models\Group;
use App\Models\GroupUser;
use App\Services\Base\BaseService;
use Exception;
use Illuminate\Support\Facades\DB;

final class GroupService extends BaseService
{

    private Group $group;
    private ExceptionService $exceptionService;
    private AuthService $authService;

    /**
     * @param Group $group
     * @param AuthService $authService
     * @param ExceptionService $exceptionService
     */
    public function __construct(Group            $group,
                                AuthService      $authService,
                                ExceptionService $exceptionService)
    {
        $this->group = $group;
        $this->exceptionService = $exceptionService;
        $this->authService = $authService;
    }

    /**
     * @param $request
     * @return array
     */
    public function all($request): array
    {
        try {
            $groupQuery = $this->group->query();
            $data = $this->formatQuery($request, $groupQuery);
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
            $group = $this->group->newInstance();
            $group->name = $request->name;
            $group->description = $request->description;
            $group->code = self::groupCodeGenerate(10);
            $group->save();
            self::addMember($user, $group, 'admin');
            DB::commit();
            return $this->serviceResponse(true, __('success.save_data'), 200, $group);
        } catch (Exception $e) {
            DB::rollback();
            $info = $this->exceptionService->getInfo($e);
            return $this->serviceResponse(false, $info['message'], $info['code'], null);
        }
    }

    /**
     * @param $group
     * @return array
     */
    public function getByModel($group): array
    {
        try {
            return $this->serviceResponse(true, __('success.get_data'), 200, $group);
        } catch (Exception $e) {
            $info = $this->exceptionService->getInfo($e);
            return $this->serviceResponse(false, $info['message'], $info['code'], null);
        }
    }

    /**
     * @param $request
     * @param $group
     * @return array
     */
    public function update($request, $group): array
    {
        try {
            $isGroupAdmin = self::isGroupAdmin($request->user('api')->id, $group->id);
            if (!$isGroupAdmin) {
                abort(403, __('fail.no_permission'));
            }
            $group->name = $request->name;
            $group->description = $request->description;
            $group->save();
            return $this->serviceResponse(true, __('success.save_data'), 200, $group);
        } catch (Exception $e) {
            $info = $this->exceptionService->getInfo($e);
            return $this->serviceResponse(false, $info['message'], $info['code'], null);
        }
    }

    /*
     * user apply to join group
     */
    /**
     * @param $request
     * @return array
     */
    public function joinGroup($request): array
    {
        try {
            $group = Group::query()->where('code', $request->code)->first();
            self::addMember($request->user('api'), $group, 'member');
            return $this->serviceResponse(true, __('success.group_joined_success'), 200, $group);
        } catch (Exception $e) {
            $info = $this->exceptionService->getInfo($e);
            return $this->serviceResponse(false, $info['message'], $info['code'], null);
        }
    }

    /**
     * @param $request
     * @return array
     */
    public function groupInfo($request): array
    {
        try {
            $user = $request->user('api');
            $groupUser = GroupUser::query()->where('user_id', $user->id)->first();
            if (!isset($groupUser)) {
                abort(404, __('fail.group_not_found'));
            }
            $group = Group::query()->where('id', $groupUser->group_id)->first();
            $checkUserInGroup = GroupUser::query()->where('user_id', $user->id)->where('group_id', $group->id)->exists();
            if (!$checkUserInGroup) {
                abort(404, __('fail.group_not_found'));
            }
            $groupUserQuery = GroupUser::query()->where('group_id', $group->id)
                ->with(['user']);
            if (isset($request->ignore_self)) {
                $groupUserQuery->where('user_id', '!=', $user->id);
            }
            $groupUser = $this->formatQuery($request, $groupUserQuery);
            $groupUser->transform(function ($user) {
                return $this->authService->mapPointAndHeatScore($user);
            });
            $data = [
                'group_info' => $group,
                'group_user' => $groupUser
            ];
            return $this->serviceResponse(true, __('success.get_data'), 200, $data);
        } catch (Exception $e) {
            $info = $this->exceptionService->getInfo($e);
            return $this->serviceResponse(false, $info['message'], $info['code'], null);
        }

    }

    /**
     * @param $user
     * @param $group
     * @param $type
     * @return void
     */
    public function addMember($user, $group, $type): void
    {
        $checkExistsUser = GroupUser::query()->where('user_id', $user->id)->where('group_id', $group->id)->exists();
        if ($checkExistsUser) {
            abort(400, __('fail.user_already_in_group'));
        }
        $groupUser = new GroupUser();
        $groupUser->user_id = $user->id;
        $groupUser->group_id = $group->id;
        $groupUser->type = $type;
        $groupUser->save();
    }

    /**
     * @param $userId
     * @param $groupId
     * @return bool
     */
    public function isGroupAdmin($userId, $groupId): bool
    {
        return GroupUser::query()
            ->where('user_id', $userId)
            ->where('group_id', $groupId)
            ->where('type', 'admin')
            ->exists();
    }

    /**
     * @param $request
     * @return array
     */
    public function userHasGroup($request): array
    {
        try {
            $groupUser = GroupUser::query()->where('user_id', $request->user('api')->id)->first() ?? null;
            $code = 200;
            $message = __('success.get_data');
            $status = true;
            if (!isset($groupUser)) {
                $code = 404;
                $message = __('fail.user_has_no_group');
                $status = false;
            }
            return $this->serviceResponse($status, $message, $code, $groupUser);
        } catch (Exception $e) {
            $info = $this->exceptionService->getInfo($e);
            return $this->serviceResponse(false, $info['message'], $info['code'], null);
        }
    }

    /**
     * @param $length
     * @return string
     */
    public function groupCodeGenerate($length): string
    {
        $length = $length ?? 8;
        $code = strtoupper(substr(md5(rand()), 0, $length));
        $isExists = Group::query()->where('code', $code)->exists();
        if ($isExists) {
            return self::groupCodeGenerate($length);
        }
        return $code;
    }


    /**
     * @param $request
     * @return mixed
     */
    public function checkGroupId($request): mixed
    {
        $checkGroupId = self::userHasGroup($request);
        if (!$checkGroupId['success']) {
            abort(404, $checkGroupId['message']);
        }
        return $checkGroupId['data']['group_id'];
    }
}
