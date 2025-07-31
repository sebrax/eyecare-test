<?php

namespace Tests\Feature;

use App\Models\Exam\Exam;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExamPdfControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Testa se o PDF é gerado corretamente e retornado na resposta.
     */
    public function test_generate_pdf_returns_pdf_file()
    {
        $exams = Exam::factory()->count(3)->create();

        $payload = [
            'exam_ids' => $exams->pluck('id')->toArray(),
        ];

        $response = $this->postJson('/api/v1/exams/generate-pdf', $payload);

        $response->assertStatus(200);

        // Verifica se o header Content-Type é application/pdf
        $response->assertHeader('Content-Type', 'application/pdf');

        // Verifica se o conteúdo do PDF não está vazio
        $this->assertNotEmpty($response->getContent());
    }

    /**
     * Testa se o campo 'exam_ids' é obrigatório para gerar o PDF.
     */
    public function test_generate_pdf_requires_exam_ids()
    {
        $response = $this->postJson('/api/v1/exams/generate-pdf', []);

        $response->assertStatus(422);

        $response->assertJsonValidationErrors(['exam_ids']);
    }

    /**
     * Testa se IDs inválidos de exame causam erro de validação.
     */
    public function test_generate_pdf_with_invalid_exam_ids()
    {
        $payload = [
            'exam_ids' => [9999, 8888], // IDs que não existem
        ];

        $response = $this->postJson('/api/v1/exams/generate-pdf', $payload);

        $response->assertStatus(422);

        $response->assertJsonValidationErrors(['exam_ids.0', 'exam_ids.1']);
    }
}
