<?php

namespace App\Repositories;

use App\Models\Task;
use Illuminate\Database\Eloquent\Collection;

class TaskRepository
{
    public function getTasksDueTomorrow(): Collection
    {
        return Task::whereDate('due_date', now()->addDay())
            ->where('status', '!=', Task::STATUS_COMPLETED)
            ->with('user')
            ->get()
            ->groupBy('user_id');
    }

    public function findTasksCompletedForOneYearOrMore(): Collection
    {
        return Task::where('status', Task::STATUS_COMPLETED)
            ->where('updated_at', '<=', now()->subYear())
            ->get();
    }

    public function delete(Task $task): bool
    {
        return $task->delete();
    }
}
