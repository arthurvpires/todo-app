<?php

namespace App\Jobs;

use App\Mail\TaskReminderMail;
use App\Models\Task;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Mail;

class SendTaskReminderEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public User $user;
    public Collection $tasks;

    public function __construct(User $user, Collection $tasks)
    {
        $this->user = $user;
        $this->tasks = $tasks;
    }

    public function handle(): void
    {
        Mail::to($this->user->email)->send(new TaskReminderMail($this->user, $this->tasks));
    }
}
