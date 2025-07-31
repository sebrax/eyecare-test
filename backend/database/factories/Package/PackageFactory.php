<?php

namespace Database\Factories\Package;

use App\Models\Package\Package;
use Illuminate\Database\Eloquent\Factories\Factory;

class PackageFactory extends Factory
{
    protected $model = Package::class;

    public function definition()
    {
        $packageNames = [
            'Check-up Básico',
            'Check-up Completo',
            'Pacote Cardiológico',
            'Pacote Hormonal',
            'Pacote Preventivo',
            'Pacote Metabólico',
            'Exames Laboratoriais Gerais',
            'Pacote Endócrino',
            'Pacote Imunológico',
            'Pacote Oncológico',
            'Pacote Ginecológico',
            'Pacote Masculino',
            'Pacote Esportivo',
            'Pacote Saúde da Mulher',
            'Pacote Saúde do Homem',
        ];

        return [
            'name' => $this->faker->randomElement($packageNames),
            'observations' => $this->faker->optional()->sentence,
        ];
    }
}
