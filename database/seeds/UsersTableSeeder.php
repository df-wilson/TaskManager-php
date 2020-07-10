<?php

use Illuminate\Database\Seeder;
use Illuminate\Foundation\Auth\User;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::create([
            'name' => 'Tester',
            'email' => 'tester@test.com',
            'password' => bcrypt('$Tester1'),
        ]);

        User::create([
            'name' => 'Tester2',
            'email' => 'tester2@test.com',
            'password' => bcrypt('$Tester2'),
        ]);
    }
}
