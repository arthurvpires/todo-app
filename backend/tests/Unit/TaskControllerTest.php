<?php

namespace Tests\Unit;

use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class TaskControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $adminUser;

    protected $normalUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->adminUser = User::factory()->create([
            'is_admin' => true,
        ]);

        $this->normalUser = User::factory()->create([
            'is_admin' => false,
        ]);
    }

    #[Test]
    public function admin_pode_criar_tarefa()
    {
        $payload = [
            'user_id' => $this->normalUser->id,
            'title' => 'Tarefa teste',
            'description' => 'Descrição da tarefa teste',
            'status' => Task::STATUS_PENDING,
            'due_date' => now()->addDay(),
        ];

        $response = $this->actingAs($this->adminUser, 'sanctum')
            ->postJson('/api/tasks', $payload);

        $response->assertStatus(201)
            ->assertJsonFragment([
                'title' => 'Tarefa teste',
                'description' => 'Descrição da tarefa teste',
                'user_id' => $this->normalUser->id,
            ]);

        $this->assertDatabaseHas('tasks', [
            'title' => 'Tarefa teste',
            'user_id' => $this->normalUser->id,
        ]);
    }

    #[Test]
    public function usuario_comum_nao_pode_criar_tarefa()
    {
        $payload = [
            'user_id' => $this->normalUser->id,
            'title' => 'Tarefa teste',
            'status' => Task::STATUS_PENDING,
        ];

        $response = $this->actingAs($this->normalUser, 'sanctum')
            ->postJson('/api/tasks', $payload);

        $response->assertStatus(403)
            ->assertJson([
                'message' => 'Apenas administradores podem criar tarefas',
            ]);
    }

    #[Test]
    public function usuario_pode_listar_suas_tarefas()
    {
        Task::factory()->count(3)->create(['user_id' => $this->normalUser->id]);
        Task::factory()->count(2)->create(['user_id' => $this->adminUser->id]);

        $response = $this->actingAs($this->normalUser, 'sanctum')
            ->getJson('/api/tasks');

        $response->assertStatus(200)
            ->assertJsonCount(3);
    }

    #[Test]
    public function usuario_pode_atualizar_sua_tarefa()
    {
        $task = Task::factory()->create([
            'user_id' => $this->normalUser->id,
            'status' => Task::STATUS_PENDING,
        ]);

        $payload = [
            'title' => 'Título atualizado',
            'description' => 'Descrição atualizada',
            'status' => Task::STATUS_COMPLETED,
        ];

        $response = $this->actingAs($this->normalUser, 'sanctum')
            ->putJson("/api/tasks/{$task->id}", $payload);

        $response->assertStatus(200)
            ->assertJsonFragment([
                'title' => 'Título atualizado',
                'description' => 'Descrição atualizada',
                'status' => Task::STATUS_COMPLETED,
            ]);

        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'title' => 'Título atualizado',
            'status' => Task::STATUS_COMPLETED,
        ]);
    }

    #[Test]
    public function usuario_nao_pode_atualizar_tarefa_de_outro_usuario()
    {
        $task = Task::factory()->create(['user_id' => $this->adminUser->id]);

        $payload = [
            'title' => 'Alteração inválida',
        ];

        $response = $this->actingAs($this->normalUser, 'sanctum')
            ->putJson("/api/tasks/{$task->id}", $payload);

        $response->assertStatus(403)
            ->assertJson([
                'message' => 'Acesso não autorizado',
            ]);
    }

    #[Test]
    public function usuario_pode_excluir_sua_tarefa()
    {
        $task = Task::factory()->create(['user_id' => $this->normalUser->id]);

        $response = $this->actingAs($this->normalUser, 'sanctum')
            ->deleteJson("/api/tasks/{$task->id}");

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Tarefa excluída com sucesso',
            ]);

        $this->assertDatabaseMissing('tasks', ['id' => $task->id]);
    }

    #[Test]
    public function usuario_nao_pode_excluir_tarefa_de_outro_usuario()
    {
        $task = Task::factory()->create(['user_id' => $this->adminUser->id]);

        $response = $this->actingAs($this->normalUser, 'sanctum')
            ->deleteJson("/api/tasks/{$task->id}");

        $response->assertStatus(403)
            ->assertJson([
                'message' => 'Acesso não autorizado',
            ]);
    }
}
