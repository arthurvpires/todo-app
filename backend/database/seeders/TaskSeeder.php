<?php

namespace Database\Seeders;

use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Seeder;

class TaskSeeder extends Seeder
{
    public function run(): void
    {
        $userId = User::where('email', 'admin@example.com')->value('id');
        $tasks = [
            [
                'user_id' => $userId,
                'title' => 'Fazer compras do mercado',
                'description' => 'Ir até o Carrefour e comprar frutas, verduras e carne.',
                'due_date' => now()->addDays(2)->toDateString(),
                'status' => Task::STATUS_PENDING,
            ],
            [
                'user_id' => $userId,
                'title' => 'Estudar Laravel',
                'description' => 'Ler a documentação e praticar com exemplos.',
                'due_date' => now()->addDay()->toDateString(),
                'status' => Task::STATUS_IN_PROGRESS,
            ],
            [
                'user_id' => $userId,
                'title' => 'Enviar relatório mensal',
                'description' => 'Preparar e enviar o relatório de desempenho do mês passado.',
                'due_date' => now()->subDays(2)->toDateString(),
                'status' => Task::STATUS_COMPLETED,
            ],
        ];

        // Two years old completed task
        Task::create([
            'user_id' => $userId,
            'title' => 'Tarefa antiga',
            'description' => 'Esta tarefa foi concluída há mais de um ano.',
            'updated_at' => now()->subYears(2)->toDateString(),
            'status' => Task::STATUS_COMPLETED,
        ]);


        foreach ($tasks as $data) {
            Task::create($data);
        }
    }
}
