<?php

namespace Tests\Feature;

use App\Http\Repository\TodosRepository;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class TodoItemTest extends TestCase
{
    use RefreshDatabase;
    
    public function testCreateTodo()
    {
        logger("TodoItemTest::testCreateTodo - Enter");

        $userId = 1;
        $description = "This is a test todo";
        $status = "Complete";
        $priority = "Medium";
        $dueDate = "2020-10-23";

        // Test with unauthorized user
        $response = $this->postJson('/api/todo',
            [
                'description' => $description,
                'status' => $status,
                'priority' => $priority,
                'due_at' => $dueDate
            ]);

        $response->assertStatus(401)
            ->assertJson([
                'msg' => 'not authorized',
                'id'  => 0
            ]);

        // Test with authorized user
        $user = factory(User::class)->create();

        $response = $this->actingAs($user)->postJson('/api/todo',
            [
                'description' => $description,
                'status' => $status,
                'priority' => $priority,
                'due_at' => $dueDate
            ]);

        $response->assertStatus(201)
                 ->assertJson([
                     'msg' => 'created',
                     'id'  => 1
                 ]);

        $this->assertDatabaseHas('todos', [
            'id'          => 1,
            'user_id'     => $user->id,
            'description' => $description,
            'status'      => $status,
            'priority'    => $priority
        ]);

        logger("TodoItemTest::testCreateTodo - Leave");
    }

    public function testGetAllTodos()
    {
        logger("TodoItemTest::testGetAllTodos - Enter");

        $this->seed();

        // Test unauthorized user
        $response = $this->get('/api/todo');

        $response->assertStatus(401)
            ->assertJson([
                'msg' => 'not authorized'
            ]);

        // Test authorized user, no todos
        $user = factory(User::class)->create();
        $response = $this->actingAs($user)->get('/api/todo');

        $response->assertStatus(200)
            ->assertJson([
                'msg' => 'ok'
            ]);

        // Test authorized user with todos
        $user = User::find(1);

        $response = $this->actingAs($user)->get('/api/todo');

        $response->assertStatus(200)
            ->assertJson([
                'msg' => 'ok',
                'todos' => [
                    [
                        "id" => "1",
                        "user_id" => "1",
                        "description" => "This is the first todo for user 1.",
                        "status" => "In Progress",
                        "priority" => "High",
                        "completed_at" => null,
                        "due_at" => '2019-12-01',
                        "created_at" => '2019-10-01 12:20:02',
                        "updated_at" => '2019-10-01 12:20:02'
                    ],
                    [
                        "id" => "3",
                        "user_id" => "1",
                        "description" => "This is the second todo for user 1.",
                        "status" => "Not Started",
                        "priority" => "Low",
                        "completed_at" => null,
                        "due_at" => '2020-02-01',
                        "created_at" => '2019-10-01 12:20:02',
                        "updated_at" => '2019-10-01 12:20:02'
                    ]
                ]
            ]);

        logger("TodoItemTest::testGetAllTodos - Leave");
    }

    public function testDeleteTodo()
    {
        logger("TodoItemTest::testDeleteTodo - Enter");

        // Test unauthorized user
        $response = $this->delete('/api/todo/3');
        $response->assertStatus(401);

        $this->seed();

        // test valid user with unauthorized todo
        $user = User::find(2);
        $response = $this->actingAs($user)->json('PUT', '/api/todo/priority/3', ['priority' => 'High']);
        $response->assertStatus(404);

        // Test authorized user
        $user = User::find(1);

        $response = $this->actingAs($user)->delete('/api/todo/3');
        $response->assertStatus(200);

        logger("TodoItemTest::testDeleteTodo - Leave");
    }

    public function testUpdatePriority()
    {
        logger("TodoItemTest::testUpdatePriority - Enter");

        $this->seed();

        // Test unauthorized user
        $response = $this->put('/api/todo/priority/3');
        $response->assertStatus(401);

        // Test authorized user
        $user = User::find(1);

        // Test no priority given
        $response = $this->actingAs($user)->put('/api/todo/priority/3');
        $response->assertStatus(400);

        // Test with invalid priority
        $response = $this->actingAs($user)->json('PUT', '/api/todo/priority/3', ['priority' => 'Unknown']);
        $response->assertStatus(400);

        // Test valid requests
        $response = $this->actingAs($user)->json('PUT', '/api/todo/priority/3', ['priority' => 'High']);
        $response->assertStatus(200);

        $response = $this->actingAs($user)->json('PUT', '/api/todo/priority/3', ['priority' => 'Medium']);
        $response->assertStatus(200);

        $response = $this->actingAs($user)->json('PUT', '/api/todo/priority/3', ['priority' => 'Low']);
        $response->assertStatus(200);

        // test valid user with unauthorized todo
        $user = User::find(2);
        $response = $this->actingAs($user)->json('PUT', '/api/todo/priority/3', ['priority' => 'High']);
        $response->assertStatus(404);

        logger("TodoItemTest::testUpdatePriority - Leave");
    }

    public function testUpdateStatus()
    {
        logger("TodoItemTest::testUpdateStatus - Enter");

        $this->seed();

        // Test unauthorized user
        $response = $this->put('/api/todo/status/3');
        $response->assertStatus(401);

        // Test authorized user
        $user = User::find(1);

        // Test no priority given
        $response = $this->actingAs($user)->put('/api/todo/status/3');
        $response->assertStatus(400);

        // Test with invalid priority
        $response = $this->actingAs($user)->json('PUT', '/api/todo/status/3', ['status' => 'Unknown']);
        $response->assertStatus(400);

        // Test valid requests
        $response = $this->actingAs($user)->json('PUT', '/api/todo/status/3', ['status' => 'Not Started']);
        $response->assertStatus(200);

        $response = $this->actingAs($user)->json('PUT', '/api/todo/status/3', ['status' => 'In Progress']);
        $response->assertStatus(200);

        $response = $this->actingAs($user)->json('PUT', '/api/todo/status/3', ['status' => 'Done']);
        $response->assertStatus(200);

        // test valid user with unauthorized todo
        $user = User::find(2);
        $response = $this->actingAs($user)->json('PUT', '/api/todo/status/3', ['status' => 'In Progress']);
        $response->assertStatus(404);

        logger("TodoItemTest::testUpdateStatus - Leave");
    }

    public function testUpdateDueDate()
    {
        logger("TodoItemTest::testUpdateDueDate - Enter");

        $this->seed();

        // Test unauthorized user
        $response = $this->put('/api/todo/due/3');
        $response->assertStatus(401);

        // Test authorized user
        $user = User::find(1);

        // Test no priority given
        $response = $this->actingAs($user)->put('/api/todo/due/3');
        $response->assertStatus(400);

        // Test with invalid priority
        $response = $this->actingAs($user)->json('PUT', '/api/todo/due/3', ['due' => '2020-10-5a']);
        $response->assertStatus(400);

        // Test valid requests
        $response = $this->actingAs($user)->json('PUT', '/api/todo/due/3', ['due' => '2020-12-15']);
        $response->assertStatus(200);

        // test valid user with unauthorized todo
        $user = User::find(2);
        $response = $this->actingAs($user)->json('PUT', '/api/todo/due/3', ['due' => '2020-09-17']);
        $response->assertStatus(404);

        logger("TodoItemTest::testUpdateDueDate - Leave");
    }
}
