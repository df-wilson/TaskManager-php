<?php

namespace App\Repository;

use DateTime;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TaskRepository
{
    public function all(int $userId)
    {
        logger("TaskRepository::all - Enter.", ["User ID" => $userId]);

        $tasks = DB::select("SELECT * FROM todos WHERE user_id = ?", [$userId]);

        return $tasks;
    }

    public function create(int $userId, string $description, string $status, string $priority, string $dueAt)
    {
        logger("TaskRepository::create - Enter.", ["User ID" => $userId]);

        $id = DB::insert('INSERT INTO todos (user_id, description, status, priority, due_at, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?)',
            [
                $userId,
                $description,
                $status,
                $priority,
                $dueAt,
                new \DateTime(),
                new \DateTime()
            ]);

        $id = DB::getPdo()->lastInsertId();

        logger("TaskRepository::create - Leave.", ["Task ID" => $id]);

        return $id;
    }

    public function delete(int $userId, $taskId)
    {
        logger("TaskRepository::delete - Enter.", ["User ID" => $userId]);

        $deleted = DB::delete('DELETE FROM todos WHERE id = ? AND user_id = ?',
            [
                $taskId,
                $userId
            ]);

        logger("TaskRepository::delete - Leave.", ["Num Deleted" => $deleted]);

        return $deleted;
    }

    public function updatePriority(int $userId, int $taskId, string $priority)
    {
        logger("TaskRepository::updatePriority - Enter.", ["User ID" => $userId, "Task Id" => $taskId, "Priority" => $priority]);

        $updated = 0;

        if($priority == "High"   ||
           $priority == "Medium" ||
           $priority == "Low")
        {
            $updated = DB::update('UPDATE todos SET priority = :priority, updated_at = :updated_at where id = :id AND user_id = :user_id',
                [
                    'priority' => $priority,
                    'updated_at' => new \DateTime(),
                    'id'       => $taskId,
                    'user_id'  => $userId
                ]);

        } else {
            Log::error("TaskRepository::updatePriority - Unknown priority.", ["Priority" => $priority]);
            $updated = -1;
        }

        logger("TaskRepository::updatePriority - Leave.", ["Num Updated" => $updated]);

        return $updated;
    }

    public function updateStatus(int $userId, int $taskId, string $status)
    {
        logger("TaskRepository::updateStatus - Enter.", ["User ID" => $userId, "Task Id" => $taskId, "Status" => $status]);

        $updated = 0;

        if($status  == "Not Started" ||
            $status == "In Progress" ||
            $status == "Done")
        {
            $updated = DB::update('UPDATE todos SET status = :status, updated_at = :updated_at where id = :id AND user_id = :user_id',
                [
                    'status'  => $status,
                    'updated_at' => new \DateTime(),
                    'id'      => $taskId,
                    'user_id' => $userId
                ]);

        } else {
            Log::error("TaskRepository::updateStatus - Unknown status.", ["Status" => $status]);
            $updated = -1;
        }

        logger("TaskRepository::updateStatus - Leave.", ["Num Updated" => $updated]);

        return $updated;
    }

    public function updateDueDate(int $userId, int $taskId, string $dueDate)
    {
        logger("TaskRepository::updateDueDate - Enter.", ["User ID" => $userId, "Task Id" => $taskId, "Due Date" => $dueDate]);

        $updated = 0;
        
        if (DateTime::createFromFormat('Y-m-d', $dueDate) !== false)
        {
            $updated = DB::update('UPDATE todos SET due_at = :due_at, updated_at = :updated_at where id = :id AND user_id = :user_id',
                [
                    'due_at'  => $dueDate,
                    'updated_at' => new \DateTime(),
                    'id'      => $taskId,
                    'user_id' => $userId
                ]);

        } else {
            Log::error("TaskRepository::updateDueDate - Invalid date.", ["Due Date" => $dueDate]);
            $updated = -1;
        }

        logger("TaskRepository::updateDueDate - Leave.", ["Num Updated" => $updated]);

        return $updated;
    }
}
