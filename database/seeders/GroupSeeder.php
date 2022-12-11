<?php

namespace Database\Seeders;

use App\Models\Group;
use App\Models\GroupUser;
use App\Models\User;
use App\Services\GroupService;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\App;

class GroupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $users = User::query()->get();
        $newGroup = new Group();
        $newGroup->name = 'Teletubbies';
        $newGroup->description = 'Teletubbies is cute, fun and friendly';
        $newGroup->code = 'TLTB0023423';
        $newGroup->save();
        foreach ($users as $index => $user){
            $groupUser = new GroupUser();
            $groupUser->user_id = $user->id;
            $groupUser->group_id = $newGroup->id;
            $groupUser->type = ($index == 0) ? 'admin' : 'user';
            $groupUser->save();
        }
    }
}
