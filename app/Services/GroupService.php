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

    /**
     * @param Group $group
     */
    public function __construct(Group $group)
    {
        $this->group = $group;
    }

    public function all($request): array
    {
        try {
            $groupQuery = $this->group->query();
            $data = $this->formatQuery($request, $groupQuery);
            return $this->serviceResponse(true, __('success.get_data'), 200, $data);
        } catch (Exception $e) {
            return $this->serviceResponse(false, $e->getMessage(), 500, null);
        }
    }

    public function save($request): array
    {
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
            return $this->serviceResponse(false, $e->getMessage(), 500, null);
        }
    }

    public function getByModel($group): array
    {
        return $this->serviceResponse(true, __('success.get_data'), 200, $group);
    }

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
            return $this->serviceResponse(false, $e->getMessage(), 500, null);
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
        $group = Group::query()->where('code', $request->code)->first();
        self::addMember($request->user('api'), $group, 'member');
        return $this->serviceResponse(true, __('success.group_joined_success'), 200, $group);
    }

    public function listGroupUser($groupId)
    {
        $group = Group::query()->where('id', $groupId)->exists();
        if (!$group) {
            abort(404, __('fail.group_not_found'));
        }
        $userGroup = GroupUser::query()->where('group_id', $groupId);
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

    public function isGroupAdmin($userId, $groupId): bool
    {
        return GroupUser::query()
            ->where('user_id', $userId)
            ->where('group_id', $groupId)
            ->where('type', 'admin')
            ->exists();
    }

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
}
