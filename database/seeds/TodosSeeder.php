<?php

use Illuminate\Database\Seeder;

class TodosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('todos')->insert([
            'user_id'    => 1,
            'description' => 'This is the first todo for user 1.',
            'status'     => "In Progress",
            'priority'   => "High",
            'due_at'     => '2019-12-01',
            'created_at' => '2019-10-01 12:20:02',
            'updated_at' => '2019-10-01 12:20:02'
        ]);

        DB::table('todos')->insert([
            'user_id' => 2,
            'description' => 'This is the first todo for user 2.',
            'status' => "Not Started",
            'priority' => "Medium",
            'due_at'     => '2019-12-11',
            'created_at' => '2019-10-01 12:20:02',
            'updated_at' => '2019-10-01 12:20:02'
        ]);

        DB::table('todos')->insert([
            'user_id' => 1,
            'description' => 'This is the second todo for user 1.',
            'status' => "Not Started",
            'priority' => "Low",
            'due_at'     => '2020-02-01',
            'created_at' => '2019-10-01 12:20:02',
            'updated_at' => '2019-10-01 12:20:02'
        ]);

    }
}
