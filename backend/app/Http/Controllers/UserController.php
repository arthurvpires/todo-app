<?php

namespace App\Http\Controllers;

use App\Http\Requests\Users\StoreRequest;
use App\Models\User;

class UserController extends Controller
{
    public function index()
    {
        return response()->json(User::all());
    }

    public function store(StoreRequest $request)
    {
        $user = User::create($request->validated());

        return response()->json($user, 201);
    }

    public function destroy(User $user)
    {
        if (! $user) {
            return response()->json(['message' => 'Usuário não encontrado'], 404);
        }

        $user->delete();

        return response()->json(['message' => 'Usuário excluído com sucesso'], 200);
    }
}
