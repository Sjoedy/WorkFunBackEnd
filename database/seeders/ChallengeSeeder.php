<?php

namespace Database\Seeders;

use App\Models\Challenge;
use App\Models\ChallengeUser;
use App\Models\GroupUser;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ChallengeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = GroupUser::query()->where('type', 'admin')->first();
        $challengeInfo = [
            [
                'title' => 'ຂຽນ function ລົງທະບຽນ',
                'description' => 'ໃຫ້ຂຽນ function ລົງທະບຽນ ເຊິ່ງເປັນການລົງທະບຽນທີ່ຕ້ອງຢືນຢັນເບີໂທຜ່ານ OTP',
                'type' => 'task',
                'group_id' => $data->id,
                'point' => 50
            ],
            [
                'title' => 'ຂຽນ function login',
                'description' => 'ໃຫ້ຂຽນ function login',
                'type' => 'task',
                'group_id' => $data->id,
                'point' => 25
            ],
            [
                'title' => 'ຂຽນ function ເພີ່ມລົບແກ້ໄຂຂໍ້ມູນສິນຄ້າ',
                'description' => 'ໃຫ້ຂຽນ function ເພີ່ມລົບແກ້ໄຂຂໍ້ມູນສິນຄ້າ',
                'type' => 'task',
                'group_id' => $data->id,
                'point' => 100
            ],
            [
                'title' => 'sport day',
                'description' => 'เຂົ້າຮ່ວມ sport day ກັບທີມງານໃນວັນສຸກນີ້',
                'type' => 'activity',
                'group_id' => $data->id,
                'point' => 100
            ]
        ];
        $statusArray = ['todo', 'doing', 'done'];
        $scoreArray = [1, 2, 3, 4, 5];
        foreach ($challengeInfo as $challenge) {
            $users = GroupUser::query()->where('type', 'user')->get();
            $newChallenge = new Challenge();
            $newChallenge->title = $challenge['title'];
            $newChallenge->description = $challenge['description'];
            $newChallenge->type = $challenge['type'];
            $newChallenge->group_id = $challenge['group_id'];
            $newChallenge->point = $challenge['point'];
            $newChallenge->save();
            foreach ($users as $user) {
                $challengeUser = new ChallengeUser();
                $challengeUser->user_id = $user->user_id;
                $challengeUser->challenge_id = $newChallenge->id;
                $statusIndex = array_rand($statusArray);
                if ($statusArray[$statusIndex] == 'done') {
                    $scoreIndex = array_rand($scoreArray);
                    $challengeUser->heat_score = $scoreArray[$scoreIndex];
                }
                $challengeUser->status = $statusArray[$statusIndex];
                $challengeUser->save();
            }

        }
    }
}
