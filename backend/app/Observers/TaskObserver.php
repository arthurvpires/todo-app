<?php

namespace App\Observers;

use App\Models\Task;
use App\Repositories\TaskRepository;
use Illuminate\Validation\ValidationException;

class TaskObserver
{
    public function creating(Task $task): void 
    {
        $exists = app(TaskRepository::class)->taskAlreadyExists($task->user->id, $task->title, $task->description);
        if ($exists) {
            throw ValidationException::withMessages([
                'title' => 'Você já criou uma tarefa com esse título.'
            ]);
        }
    }
}
