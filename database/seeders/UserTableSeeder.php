<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;


class UserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $users = User::create([
            'name' => 'Irakli', 
            'lastname' => 'Jugheli', 
            'phone' => '551360135',
            'email' => 'ijugh13@freeuni.edu.ge',
            'password' => Hash::make('gcrt123')
        ]);
    }
}