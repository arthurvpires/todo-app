<?php

namespace App\Mail;

use App\Models\User;
use App\Models\Task;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;

class TaskReminderMail extends Mailable
{
    use Queueable, SerializesModels;

    public User $user;
    public Collection $tasks;

    public function __construct(User $user, Collection $tasks)
    {
        $this->user = $user;
        $this->tasks = $tasks;
    }

    public function build()
    {
        return $this->subject('Lembrete: Tarefas com vencimento amanhã')
            ->markdown('emails.tasks.reminder')
            ->with([
                'user' => $this->user,
                'tasks' => $this->tasks,
            ]);
    }
}
