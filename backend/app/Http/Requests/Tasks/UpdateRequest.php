<?php

namespace App\Http\Requests\Tasks;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'title' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'due_date' => 'nullable|date',
            'status' => 'nullable|in:pending,in_progress,completed',
        ];
    }

    public function title(): string
    {
        return $this->validated('title');
    }

    public function description(): ?string
    {
        return $this->validated('description');
    }

    public function dueDate(): ?string
    {
        return $this->validated('due_date');
    }

    public function status(): ?string
    {
        return $this->validated('status');
    }
}
