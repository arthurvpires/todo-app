<?php

namespace App\Http\Controllers;

use App\Http\Requests\Tasks\StoreRequest;
use App\Http\Requests\Tasks\UpdateRequest;
use App\Models\Task;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function index(Request $request)
    {
        $tasks = Task::where('user_id', $request->user()->id)->get();
        return response()->json($tasks);
    }

    public function store(StoreRequest $request)
    {
        if (! $request->user()->is_admin) {
            return response()->json(['message' => 'Apenas administradores podem criar tarefas'], 403);
        }

        $task = Task::create([
            'user_id' => $request->userId(),
            'title' => $request->title(),
            'description' => $request->description(),
            'status' => $request->status(),
            'due_date' => $request->dueDate(),
        ]);

        return response()->json($task, 201);
    }

    public function show(Request $request, Task $task)
    {
        if ($task->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Acesso não autorizado'], 403);
        }

        return response()->json($task);
    }

    public function update(UpdateRequest $request, Task $task)
    {
        if ($task->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Acesso não autorizado'], 403);
        }

        $task->update($request->validated());

        return response()->json($task);
    }

    public function destroy(Request $request, Task $task)
    {
        if ($task->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Acesso não autorizado'], 403);
        }

        $task->delete();

        return response()->json(['message' => 'Tarefa excluída com sucesso']);
    }
}
