<?php

namespace App\Http\Controllers;

use App\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;
use Illuminate\Pagination\Paginator;

class ToDoListController extends Controller
{
    private $__500_message = "Something was wrong on server => ";

    private $__400_message = "Validation errors found!";

    public function index(Request $request)
    {
        try {
            $qtd = $request->has("per_page") ? $request->get("per_page") : 10;
            $page = $request->has("page") ? $request->get("page") : 1;

            Paginator::currentPageResolver(function () use ($page) {
                return $page;
            });

            $tasks = Task::paginate($qtd);

            if (empty($tasks->items)) {
                return response()->json([
                    "message" => "Good news! The task you were trying to delete didn't even exist."
                ], 200);
            }

            $tasks = $tasks->appends(Request::capture()->except('page'));

            return response()->json([
                "tasks" => $tasks
            ], 200);
        } catch (\Exception $e) {
            return $this->serverErrorMessage($e);
        }
    }

    public function store(Request $request)
    {
        try {
            $validator = $this->validateSaveTake($request);
            if ($validator->fails()) {
                $this->valitionsErrorsMessage($validator);
            }

            $dataToSave = $this->getDataToSave($request);
            $task = Task::create($dataToSave);
            Task::reorderTask($dataToSave['sort_order'], $dataToSave['sort_order']);

            return response()->json([
                "message" => "Task created with success!",
                "task" => $task
            ], 401);

        } catch (\Exception $e) {
            return $this->serverErrorMessage($e);
        }
    }

    public function show($id)
    {
        try {
            $task = Task::find($id);

            if (empty($task)) {
                return $this->notFoundReturn($id);
            }

            return response()->json([
                "task" => $task
            ], 200);

        } catch (\Exception $e) {
            return $this->serverErrorMessage($e);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $task = Task::find($id);
            $oldSortOrder = $task->sort_order;
            if (empty($task)) {
                return $this->notFoundReturn($id, "Are you a hacker or something? The task you were trying to edit doesn't exist.");
            }

            $validator = $this->validateSaveTake($request);
            if ($validator->fails()) {
                return response()->json([
                    'message' => $this->__400_message,
                    'errors' => $validator->errors()
                ], 400);
            }

            $dataToSave = $this->getDataToSave($request, $id);
            $task->update($dataToSave);
            Task::reorderTask($oldSortOrder, $dataToSave['sort_order']);
            return response()->json([
                "message" => "Task updated with success!",
                "task" => $task
            ], 200);
        } catch (\Exception $e) {
            return $this->serverErrorMessage($e);
        }
    }

    public function destroy($id)
    {
        try {
            $task = Task::find($id);

            if (empty($task)) {
                return $this->notFoundReturn($id, "Good news! The task you were trying to delete didn't even exist.");
            }

            $task->delete();

            return response()->json([
                "message" => "Task with id $id was deleted with success!"
            ], 200);
        } catch (\Exception $e) {
            return $this->serverErrorMessage($e);
        }
    }


    private function validateSaveTake($request)
    {
        return Validator::make($request->all(), [
            "type" => [
                "required",
                Rule::in(["shopping", "work"])
            ],
            "content" => "required|max:200",
            "sort_order" => "required|numeric"
        ], [
            "type.in" => "The task type you provided is not supported. You can only use shopping or work.",
            "content.required" => "Bad move! Try removing the task instead of deleting its content."
        ]);
    }

    /**
     * @param Request $request
     * @param string $uuidUpdate
     * @return array
     */
    private function getDataToSave(Request $request, $uuidUpdate = ""): array
    {
        $data = [
            "uuid" => $uuidUpdate ? $uuidUpdate : Str::uuid()->toString(),
            "type" => $request->get("type"),
            "content" => $request->get("content"),
            "sort_order" => $request->get("sort_order"),
            "done" => $uuidUpdate ? $request->get("done") : 0
        ];

        if (!$uuidUpdate) {
            $currentDate = date('Y-m-d H:i:s');
            $data['date_created'] = $currentDate;
            $data['last_update'] = $currentDate;
        }

        return $data;
    }

    /**
     * @param $id
     * @param $message
     * @return \Illuminate\Http\JsonResponse
     */
    private function notFoundReturn($id, $message): \Illuminate\Http\JsonResponse
    {
        return response()->json([
            "message" => $message,
        ], 400);
    }

    /**
     * @param \Exception $e
     * @return \Illuminate\Http\JsonResponse
     */
    private function serverErrorMessage(\Exception $e): \Illuminate\Http\JsonResponse
    {
        return response()->json([
            "message" => $this->__500_message . $e->getMessage()
        ], 500);
    }

    /**
     * @param Validator $validator
     * @return \Illuminate\Http\JsonResponse
     */
    private function valitionsErrorsMessage(Validator $validator)
    {
        return response()->json([
            'message' => $this->__400_message,
            'errors' => $validator->errors()
        ], 400);
    }
}
