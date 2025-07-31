<?php

namespace Tests\Unit;

use App\Models\Exam\Exam;
use App\Models\Package\Package;
use App\Services\Package\PackageService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PackageServiceTest extends TestCase
{
    use RefreshDatabase;

    protected PackageService $packageService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->packageService = $this->app->make(PackageService::class);
    }

    public function test_create_package_with_exams()
    {
        $exams = Exam::factory()->count(3)->create();

        $data = [
            'name' => 'Pacote Completo',
            'observations' => 'Observação teste',
            'exams' => $exams->pluck('id')->toArray(),
        ];

        $package = $this->packageService->create($data);

        $this->assertDatabaseHas('packages', ['name' => 'Pacote Completo']);
        $this->assertEquals(3, $package->exams()->count());
    }

    public function test_delete_package()
    {
        $package = Package::factory()->create();
        $deleted = $this->packageService->delete($package->id);
        $this->assertTrue($deleted);
        $this->assertSoftDeleted('packages', ['id' => $package->id]);
    }
}
