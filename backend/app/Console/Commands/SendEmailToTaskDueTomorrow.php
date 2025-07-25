<?php

namespace App\Console\Commands;

use App\Jobs\SendTaskReminderEmail;
use App\Repositories\TaskRepository;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SendEmailToTaskDueTomorrow extends Command
{
    protected $signature = 'email:send-to-task-due-tomorrow';
    protected $description = 'Send email reminders for tasks due tomorrow';

public function handle()
{
    $tasksByUser = app(TaskRepository::class)->getTasksDueTomorrow();
    if ($tasksByUser->isEmpty()) {
        $this->info('Nenhuma tarefa com vencimento amanhã.');
        return;
    }

    $sentEmails = 0;
    foreach ($tasksByUser as $tasks) {
        $user = $tasks->first()->user;
        SendTaskReminderEmail::dispatch($user, $tasks)->delay(now()->subDay(1)); 

        $sentEmails++;
        $this->info("Email enviado para usuário #{$user->id} com ". $tasks->count(). " tarefas.");
    }

    $this->info("Total de e-mails enviados: {$sentEmails}");
}

}
