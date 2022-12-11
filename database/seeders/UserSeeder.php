<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $userInfo = [
            [
                'name' => 'Joedy',
                'surname' => 'Teletubbies',
                'email' => 'joedy@gmail.com',
                'tel' => '12345678',
                'password' => 'a1234567'
            ],
            [
                'name' => 'Do',
                'surname' => 'Teletubbies',
                'email' => 'do@gmail.com',
                'tel' => '12345678',
                'password' => 'a1234567'
            ],
            [
                'name' => 'Paeng',
                'surname' => 'Teletubbies',
                'email' => 'paeng@gmail.com',
                'tel' => '12345678',
                'password' => 'a1234567'
            ],
            [
                'name' => 'Nou',
                'surname' => 'Teletubbies',
                'email' => 'nou@gmail.com',
                'tel' => '12345678',
                'password' => 'a1234567'
            ]
        ];
        foreach ($userInfo as $user){
            $newUser = new User();
            $newUser->name = $user['name'];
            $newUser->surname = $user['surname'];
            $newUser->email = $user['email'];
            $newUser->tel = $user['tel'];
            $newUser->password = Hash::make($user['password']);
            $newUser->save();
        }
    }
}
