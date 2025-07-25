<?php

namespace App\Http\Requests\Users;

use Illuminate\Foundation\Http\FormRequest;

class StoreRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'is_admin' => 'boolean',
        ];
    }

    public function name(): string
    {
        return $this->validated('name');
    }

    public function email(): string
    {
        return $this->validated('email');
    }

    public function password(): string
    {
        return $this->validated('password');
    }

    public function isAdmin(): bool
    {
        return $this->validated('is_admin', false);
    }
}
