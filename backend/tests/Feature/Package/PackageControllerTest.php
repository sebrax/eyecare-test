<?php

namespace Tests\Feature\Package;

use App\Models\Exam\Exam;
use App\Models\Package\Package;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PackageControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Testa a criação de um pacote com exames relacionados.
     */
    public function test_can_create_package()
    {
        $exams = Exam::factory()->count(2)->create();

        $payload = [
            'name' => 'Pacote Teste',
            'observations' => 'Observação qualquer',
            'exams' => $exams->pluck('id')->toArray(),
        ];

        $response = $this->postJson('/api/v1/packages', $payload);

        $response->assertStatus(201)
            ->assertJsonFragment(['name' => 'Pacote Teste'])
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'id',
                    'name',
                    'observations',
                    'exams' => [
                        '*' => ['id', 'name', 'laterality', 'comment', 'group']
                    ],
                    'created_at',
                    'updated_at'
                ]
            ]);
    }

    /**
     * Testa a listagem de pacotes.
     */
    public function test_list_packages()
    {
        Package::factory()->count(3)->create();

        $response = $this->getJson('/api/v1/packages');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    '*' => ['id', 'name', 'observations', 'exams', 'created_at', 'updated_at']
                ]
            ]);
    }

    /**
     * Testa recuperação de um pacote específico.
     */
    public function test_show_package()
    {
        $package = Package::factory()->create();

        $response = $this->getJson("/api/v1/packages/{$package->id}");

        $response->assertStatus(200)
            ->assertJsonFragment(['name' => $package->name]);
    }

    /**
     * Testa atualização de um pacote.
     */
    public function test_update_package()
    {
        $package = Package::factory()->create();

        $payload = ['name' => 'Nome Atualizado', 'exams' => []];

        $response = $this->putJson("/api/v1/packages/{$package->id}", $payload);

        $response->assertStatus(200)
            ->assertJsonFragment(['name' => 'Nome Atualizado']);
    }

    /**
     * Testa exclusão de um pacote.
     */
    public function test_delete_package()
    {
        $package = Package::factory()->create();

        $response = $this->deleteJson("/api/v1/packages/{$package->id}");

        $response->assertStatus(200)
            ->assertJsonFragment(['message' => 'Pacote removido com sucesso']);
    }

    /**
     * Testa tentativa de exclusão de pacote inexistente.
     */
    public function test_delete_non_existent_package()
    {
        $response = $this->deleteJson("/api/v1/packages/999999");

        $response->assertStatus(404)
            ->assertJsonFragment(['message' => 'Pacote não encontrado']);
    }
}
