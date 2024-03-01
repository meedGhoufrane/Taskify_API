<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TaskController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/tasks",
     *     summary="Get all tasks",
     *     @OA\Response(response="200", description="List of tasks")
     * )
     */
    public function index()
    {
        $tasks = Task::all();

        return response()->json($tasks, 200);
    }

    /**
     * @OA\Post(
     *     path="/api/tasks",
     *     summary="Create a new task",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"title"},
     *             @OA\Property(property="title", type="string", example="Task title")
     *         )
     *     ),
     *     @OA\Response(response="201", description="Task created successfully"),
     *     @OA\Response(response="422", description="Validation errors")
     * )
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
        ]);

        $task = Task::create([
            'title' => $request->input('title'),
            'user_id' => Auth::id(),
        ]);
        return response()->json($task, 201);
    }

    /**
     * @OA\Put(
     *     path="/api/tasks/{task}",
     *     summary="Update a task",
     *     @OA\Parameter(
     *         name="task",
     *         in="path",
     *         description="ID of the task",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"title"},
     *             @OA\Property(property="title", type="string", example="Updated task title")
     *         )
     *     ),
     *     @OA\Response(response="200", description="Task updated successfully"),
     *     @OA\Response(response="422", description="Validation errors"),
     *     @OA\Response(response="403", description="Forbidden")
     * )
     */
    public function update(Request $request, Task $task)
    {
        $this->authorize('update', $task);

        $request->validate([
            'title' => 'required|string|max:255',
        ]);

        $task->title = $request->input('title');
        $task->save();

        return response()->json($task, 200);
    }

    /**
     * @OA\Delete(
     *     path="/api/tasks/{task}",
     *     summary="Delete a task",
     *     @OA\Parameter(
     *         name="task",
     *         in="path",
     *         description="ID of the task",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response="204", description="Task deleted successfully"),
     *     @OA\Response(response="403", description="Forbidden")
     * )
     */
    public function destroy(Task $task)
    {
        $this->authorize('delete', $task);
        $task->delete();

        return response()->json(null, 204);
    }

    /**
     * @OA\Post(
     *     path="/api/tasks/{task}/complete",
     *     summary="Mark a task as complete",
     *     @OA\Parameter(
     *         name="task",
     *         in="path",
     *         description="ID of the task",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response="200", description="Task marked as complete"),
     *     @OA\Response(response="403", description="Forbidden")
     * )
     */
    public function complete(Task $task)
    {
        $this->authorize('update', $task);

        $task->completed = true;
        $task->save();

        return response()->json($task, 200);
    }

    /**
     * @OA\Post(
     *     path="/api/tasks/{task}/incomplete",
     *     summary="Mark a task as incomplete",
     *     @OA\Parameter(
     *         name="task",
     *         in="path",
     *         description="ID of the task",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response="200", description="Task marked as incomplete"),
     *     @OA\Response(response="403", description="Forbidden")
     * )
     */
    public function incomplete(Task $task)
    {
        $this->authorize('update', $task);

        $task->completed = false;
        $task->save();

        return response()->json($task, 200);
    }
}
