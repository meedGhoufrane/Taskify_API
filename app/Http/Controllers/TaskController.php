<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TaskController extends Controller
{
   
    public function index()
    {
        $tasks = Task::all();

        return response()->json($tasks, 200);
    }
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


    public function destroy(Task $task)
    {
        $this->authorize('delete', $task);
        $task->delete();

        return response()->json(null, 204);
    }

    
    public function complete(Task $task)
    {
        $this->authorize('update', $task);

        $task->completed = true;
        $task->save();

        return response()->json($task, 200);
    }


    public function incomplete(Task $task)
    {
        $this->authorize('update', $task);

        $task->completed = false;
        $task->save();

        return response()->json($task, 200);
    }
}
