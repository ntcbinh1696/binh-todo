<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Task;
use Illuminate\Http\Request;
use App\Http\Resources\TaskResource;
use Illuminate\Support\Facades\Validator;

class TaskController extends Controller
{
    /**
     * Get tasks according to user
     */
    public function getTasks(Request $request)
    {
        $user = User::where('id', '=', $request->query('userId'))->first();
        if ($user === null) {
            return response()->json([
                "message" => "This user isn't exists"
            ], 500);
        }
        if (Task::where('user_id', $request->query('userId'))->exists()) {
            $tasks = Task::where('user_id', $request->query('userId'))->orderByDesc('is_pin')->orderByDesc('updated_at')->orderBy('is_done')->get();
            return response()->json($tasks);
        } else {
            return response()->json([
                "message" => "This user haven't task"
            ], 404);
        }
    }

    /**
     * Add task according to user
     */
    public function addTask(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'content' => 'required',
            'userId' => 'required',
            'isDone' => 'required',
            'isDeleted' => 'required',
            'isPin' => 'required'
        ]);

        if($validator->fails()){
            $response = [
                'success' => false,
                'message' => $validator->messages()
            ];
            return response()->json($response, 404);
        }

        $user = User::where('id', '=', $request->userId)->first();

        if ($user === null) {
            return response()->json([
                "message" => "User isn't existed. Can not create task with this user!"
            ], 500);
        }
        // try to create the task
        try {
            $input = [
                'content' => $request->content,
                'user_id' => $request->userId,
                'is_done' => $request->isDone,
                'is_deleted' => $request->isDeleted,
                'is_pin' => $request->isPin
            ];
            Task::create($input);

            $success = true;
            $message = "Task is created successfully";
        } catch (\Illuminate\Database\QueryException $ex) {
            $success = false;
            $data = null;
            $message = $ex->getMessage();
        }

        // make response
        $response = [
            'success' => $success,
            'message' => $message
        ];

        // return response
        return response()->json($response);

    }

    /**
     * Update task
     */
    public function updateTask(Request $request, $id)
    {

        $validator = Validator::make($request->all(), [
            'content' => 'required',
            'userId' => 'required',
            'isDone' => 'required',
            'isDeleted' => 'required',
            'isPin' => 'required'
        ]);

        if($validator->fails()){
            $response = [
                'success' => false,
                'message' => $validator->messages()
            ];
            return response()->json($response, 404);
        }

        $user = User::where('id', '=', $request->userId)->first();

        if ($user === null) {
            return response()->json([
                "message" => "User isn't existed. Can not update task for this user!"
            ], 500);
        }

        // try to create the task
        try {
            $input = [
                'content' => $request->content,
                'user_id' => $request->userId,
                'is_done' => $request->isDone,
                'is_deleted' => $request->isDeleted,
                'is_pin' => $request->isPin
            ];
            $task=Task::find($id);
            if ($task === null) {
                return response()->json([
                    "message" => "Invalid task id!"
                ], 500);
            }

            $task->update($input);

            $success = true;
            $data = $task;
            $message = "Task is created successfully";
        } catch (\Illuminate\Database\QueryException $ex) {
            $success = false;
            $data = null;
            $message = $ex->getMessage();
        }

        // make response
        $response = [
            'success' => $success,
            'message' => $message
        ];

        // return response
        return response()->json($response, 200);

    }
}
