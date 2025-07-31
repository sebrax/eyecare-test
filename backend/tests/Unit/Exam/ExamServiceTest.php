<?php

namespace Tests\Unit;

use App\Models\Exam\Exam;
use App\Services\Exam\ExamService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExamServiceTest extends TestCase
{
    use RefreshDatabase;

    protected ExamService $examService;

    protected function setUp(): void
    {
        parent::setUp();

        // Instancia o serviço para uso nos testes
        $this->examService = $this->app->make(ExamService::class);
    }

    /**
     * Testa se o serviço cria um exame corretamente no banco.
     */
    public function test_create_exam()
    {
        $data = [
            'name' => 'Teste de Visão',
            'laterality' => 'OD',
            'comment' => 'Comentário do exame',
            'group' => 'Individual',
        ];

        $exam = $this->examService->create($data);

        // Verifica se o exame está registrado na base de dados
        $this->assertDatabaseHas('exams', ['name' => 'Teste de Visão']);

        // Verifica se o campo laterality está correto
        $this->assertEquals('OD', $exam->laterality);
    }

    /**
     * Testa se o serviço retorna corretamente exames por seus IDs.
     */
    public function test_get_by_ids_returns_correct_exams()
    {
        // Cria 3 exames no banco
        $exams = Exam::factory()->count(3)->create();

        // Busca esses exames via serviço usando seus IDs
        $fetchedExams = $this->examService->getByIds($exams->pluck('id')->toArray());

        // Verifica se retornou a quantidade correta de exames
        $this->assertCount(3, $fetchedExams);
    }

    /**
     * Testa se atualizar um exame funciona corretamente.
     */
    public function test_update_exam()
    {
        $exam = Exam::factory()->create();

        $updatedData = [
            'name' => 'Exame Atualizado',
            'laterality' => 'OE',
            'comment' => 'Comentário atualizado',
            'group' => 'Grupo 1',
        ];

        $updatedExam = $this->examService->update($exam->id, $updatedData);

        $this->assertEquals('Exame Atualizado', $updatedExam->name);
        $this->assertEquals('OE', $updatedExam->laterality);
        $this->assertDatabaseHas('exams', ['name' => 'Exame Atualizado']);
    }

    /**
     * Testa se deletar um exame funciona e retorna true.
     */
    public function test_delete_exam()
    {
        $exam = Exam::factory()->create();

        $deleted = $this->examService->delete($exam->id);

        $this->assertTrue($deleted);

        $this->assertSoftDeleted('exams', ['id' => $exam->id]);
    }

    /**
     * Testa se deletar um exame que não existe retorna false.
     */
    public function test_delete_non_existent_exam_returns_false()
    {
        $deleted = $this->examService->delete(9999);

        $this->assertFalse($deleted);
    }
}
