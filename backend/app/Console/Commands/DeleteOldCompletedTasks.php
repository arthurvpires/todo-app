<?php

namespace App\Console\Commands;

use App\Repositories\TaskRepository;
use Illuminate\Console\Command;

class DeleteOldCompletedTasks extends Command
{
    protected $signature = 'delete-old-completed-tasks';
    protected $description = 'Delete tasks that were completed more than one year ago';
    protected TaskRepository $taskRepo;

    public function __construct(TaskRepository $taskRepo)
    {
        parent::__construct();
        $this->taskRepo = $taskRepo;
    }

    public function handle()
    {
        $tasks = $this->taskRepo->findTasksCompletedForOneYearOrMore();

        if ($tasks->isEmpty()) {
            $this->info('No completed tasks older than one year found.');

            return;
        }

        foreach ($tasks as $task) {
            $this->taskRepo->delete($task);
            $this->info('Deleted task: '.$task->title);
        }

        $this->info('All completed tasks older than one year have been deleted.');
    }
}
