<?php

namespace Database\Factories\Exam;

use App\Enums\Exam\GroupEnum;
use App\Enums\Exam\LateralityEnum;
use App\Models\Exam\Exam;
use Illuminate\Database\Eloquent\Factories\Factory;

class ExamFactory extends Factory
{
    protected $model = Exam::class;

    public function definition()
    {
        $examNames = [
            'Hemograma',
            'Urina tipo I',
            'Glicemia',
            'Colesterol',
            'Raio-X',
            'Eletrocardiograma',
            'Ultrassom',
            'Tomografia',
            'Ressonância Magnética',
            'Exame de Fezes',
            'Ecocardiograma'
        ];

        return [
            'name' => $this->faker->randomElement($examNames),
            'laterality' => $this->faker->randomElement([
                null,
                ...array_map(fn($e) => $e->value, LateralityEnum::cases())
            ]),
            'comment' => $this->faker->sentence,
            'group' => $this->faker->randomElement(
                array_map(fn($e) => $e->value, GroupEnum::cases())
            ),
        ];
    }
}
