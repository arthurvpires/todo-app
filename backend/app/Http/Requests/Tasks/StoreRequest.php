<?php

namespace App\Http\Requests\Tasks;

use Illuminate\Foundation\Http\FormRequest;

class StoreRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'user_id' => 'required|exists:users,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|string|in:pending,in_progress,completed',
            'due_date' => 'nullable|date|after_or_equal:today',
        ];
    }

    public function userId(): int
    {
        return $this->validated('user_id');
    }

    public function title(): string
    {
        return $this->validated('title');
    }

    public function description(): ?string
    {
        return $this->validated('description') ?? null;
    }

    public function dueDate(): ?string
    {
        return $this->validated('due_date') ?? null;
    }

    public function status(): string
    {
        return $this->validated('status');
    }
}
