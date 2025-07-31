<?php

namespace Tests\Feature\Exam;

use App\Models\Exam\Exam;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExamControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Testa a listagem paginada de exames.
     */
    public function test_can_list_exams()
    {
        Exam::factory()->count(5)->create();

        $response = $this->getJson('/api/v1/exams');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    '*' => ['id', 'name', 'laterality', 'comment', 'group', 'created_at', 'updated_at'],
                ]
            ]);
    }

    /**
     * Testa a criação de um exame.
     */
    public function test_can_create_exam()
    {
        $payload = [
            'name' => 'Exame Teste',
            'laterality' => 'OD',
            'comment' => 'Comentário de teste',
            'group' => 'Grupo 1',
        ];

        $response = $this->postJson('/api/v1/exams', $payload);

        $response->assertStatus(201)
            ->assertJsonFragment(['name' => 'Exame Teste'])
            ->assertJsonStructure(['success', 'message', 'data']);

        $this->assertDatabaseHas('exams', ['name' => 'Exame Teste']);
    }

    /**
     * Testa a recuperação de um exame específico.
     */
    public function test_can_show_exam()
    {
        $exam = Exam::factory()->create();

        $response = $this->getJson("/api/v1/exams/{$exam->id}");

        $response->assertStatus(200)
            ->assertJsonFragment(['name' => $exam->name])
            ->assertJsonStructure(['success', 'message', 'data']);
    }

    /**
     * Testa a atualização dos dados de um exame.
     */
    public function test_can_update_exam()
    {
        $exam = Exam::factory()->create();

        $payload = [
            'name' => 'Exame Atualizado',
            'laterality' => 'OE',
            'comment' => 'Comentário atualizado',
            'group' => 'Grupo 2',
        ];

        $response = $this->putJson("/api/v1/exams/{$exam->id}", $payload);

        $response->assertStatus(200)
            ->assertJsonFragment(['name' => 'Exame Atualizado'])
            ->assertJsonStructure(['success', 'message', 'data']);

        $this->assertDatabaseHas('exams', ['name' => 'Exame Atualizado']);
    }

    /**
     * Testa a exclusão de um exame.
     */
    public function test_can_delete_exam()
    {
        $exam = Exam::factory()->create();

        $response = $this->deleteJson("/api/v1/exams/{$exam->id}");

        $response->assertStatus(200)
            ->assertJsonFragment(['message' => 'Exame removido com sucesso']);

        $this->assertSoftDeleted('exams', ['id' => $exam->id]);
    }
}
