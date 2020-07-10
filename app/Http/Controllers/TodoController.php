<?php

namespace App\Http\Controllers;

use App\Http\Repository\TodosRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class TodoController extends Controller
{
    public function all()
    {
        $message = "server error";
        $statusCode = 500;
        $todos = [];

        if(Auth::check()) {
            $todoRepository = new TodosRepository();
            $todos = $todoRepository->all(Auth::id());
            $message = "ok";
            $statusCode = 200;
        } else {
            $message = "not authorized";
            $statusCode = 401;
        }

        return response()
            ->json(
            [
                'msg' => $message,
                'todos' => $todos
            ], $statusCode);
    }

    
    public function delete(int $id)
    {
        logger("TodoController::delete - Enter");

        $message = "not authorized";
        $statusCode = 401;
        $numDeleted = 0;

        if(Auth::check()) {
            $todoRepository = new TodosRepository();
            $numDeleted = $todoRepository->delete(Auth::id(), $id);

            if($numDeleted == 0) {
                $message = "not found";
                $statusCode = 404;
            } else if($numDeleted == 1) {
                $message = "deleted";
                $statusCode = 200;
            }
            else {
                Log::error("TodoController::delete - ERROR: More than 1 record deleted. ", ["Num Deleted" => $numDeleted]);
            }
        } else {
            $message = "not authorized";
            $statusCode = 401;
        }

        logger("TodoController::delete - Leave");

        return response()
            ->json(
                [
                    'msg' => $message,
                ], $statusCode);
    }

    public function store(Request $request)
    {
        logger("TodoController::store - Enter");

        $message = "server error";
        $statusCode = 500;
        $id = 0;
        $isError = false;

        if(Auth::check()) {
            $description = $request->input('description');

            // Check there is a valid status
            $status = "";
            if($this->isValidStatus($request->input('status'))) {
                $status = $request->input('status');
            } else {
                Log::error("TodoController::store - Error. Invalid status: ", ["Status" => $request->input('status')]);
                $isError = true;
                $message = "Invalid status";
            }

            // Check that there is a valid priority
            $priority = "";

            if($this->isValidPriority($request->input('priority'))) {
                $priority = $request->input('priority');
            } else {
                Log::error("TodoController::store - Error. Invalid priority: ", ["Priority" => $request->input('priority')]);
                $isError = true;
                $message = "Invalid priority";
            }

            // Check there is a valid date
            if($request->input('due_at') == "") {
                $isError = true;
                $message = "Invalid date";
            }

            if($isError) {
                $statusCode = 400;
            } else {
                $todoRepository = new TodosRepository();
                $id = $todoRepository->create(Auth::id(),
                    $description,
                    $status,
                    $priority,
                    $request->input('due_at'));

                $message = "created";
                $statusCode = 201;
            }

        } else {
            $message = "not authorized";
            $statusCode = 401;
        }

        logger("TodoController::store - Leave. ", ["Message => $message", "Todo Id" => $id]);

        return response()
            ->json(
                [
                    'msg' => $message,
                    'id'  => $id
                ], 
                $statusCode);
    }

    public function updatePriority(int $todoId, Request $request)
    {
        logger("TodoController::updatePriority - Enter", ["Todo Id" => $todoId]);

        $message = "server error";
        $statusCode = 500;

        if(Auth::check()) {
            $priority = $request->input('priority');
            if($priority) {
                $repository = new TodosRepository();
                $numUpdated = $repository->updatePriority(Auth::id(),
                                                          $todoId,
                                                          $priority);

                if($numUpdated == -1) {
                    $message = "unknown priority";
                    $statusCode = 400;
                }
                else if($numUpdated == 0) {
                    $message = "no todos updated";
                    $statusCode = 404;
                } else if($numUpdated == 1) {
                    $message = "ok";
                    $statusCode = 200;
                } else {
                    Log::error("More than 1 todo updated. ", ["Num Updated" => $numUpdated]);
                    $message = "ok";
                    $statusCode = 200;
                }
            } else {
                $message = "no priority provided";
                $statusCode = 400;
            }
        } else {
            $message = "not authorized";
            $statusCode = 401;
        }

        logger("TodoController::updatePriority - Leave", ["Status Code" => $statusCode, "Message" => $message]);

        return response()
            ->json(
                [
                    'msg' => $message,
                ],
                $statusCode);
    }

    public function updateStatus(int $todoId, Request $request)
    {
        logger("TodoController::updateStatus - Enter", ["Todo Id" => $todoId]);

        $message = "server error";
        $statusCode = 500;

        if(Auth::check()) {
            $status = $request->input('status');
            if($status) {
                $repository = new TodosRepository();
                $numUpdated = $repository->updateStatus(Auth::id(),
                                                        $todoId,
                                                        $status);

                if($numUpdated == -1) {
                    $message = "unknown status";
                    $statusCode = 400;
                }
                else if($numUpdated == 0) {
                    $message = "no todos updated";
                    $statusCode = 404;
                } else if($numUpdated == 1) {
                    $message = "ok";
                    $statusCode = 200;
                } else {
                    Log::error("More than 1 todo updated. ", ["Num Updated" => $numUpdated]);
                    $message = "ok";
                    $statusCode = 200;
                }
            } else {
                $message = "no priority provided";
                $statusCode = 400;
            }
        } else {
            $message = "not authorized";
            $statusCode = 401;
        }

        logger("TodoController::updateStatus - Leave", ["Status Code" => $statusCode, "Message" => $message]);

        return response()
            ->json(
                [
                    'msg' => $message,
                ],
                $statusCode);
    }

    public function updateDueDate(int $todoId, Request $request)
    {
        logger("TodoController::updateDueDate - Enter", ["Todo Id" => $todoId]);

        $message = "server error";
        $statusCode = 500;

        if(Auth::check()) {
            $due = $request->input('due');
            if($due) {
                $repository = new TodosRepository();
                $numUpdated = $repository->updateDueDate(Auth::id(),
                                                         $todoId,
                                                         $due);

                if($numUpdated == -1) {
                    $message = "unknown status";
                    $statusCode = 400;
                }
                else if($numUpdated == 0) {
                    $message = "no todos updated";
                    $statusCode = 404;
                } else if($numUpdated == 1) {
                    $message = "ok";
                    $statusCode = 200;
                } else {
                    Log::error("More than 1 todo updated. ", ["Num Updated" => $numUpdated]);
                    $message = "ok";
                    $statusCode = 200;
                }
            } else {
                $message = "no date provided";
                $statusCode = 400;
            }
        } else {
            $message = "not authorized";
            $statusCode = 401;
        }

        logger("TodoController::updateDueDate - Leave", ["Status Code" => $statusCode, "Message" => $message]);

        return response()
            ->json(
                [
                    'msg' => $message,
                ],
                $statusCode);
    }

    private function isValidPriority($priority)
    {
        $isValid = false;

        if($priority == "High"   ||
           $priority == "Medium" ||
           $priority == "Low")
        {
            $isValid = true;
        }

        return $isValid;
    }

    private function isValidStatus($status)
    {
        $isValid = false;

        if($status == "Not Started" ||
           $status == "In Progress" ||
           $status == "Completed")
        {
            $isValid = true;
        }

        return $isValid;
    }
}
