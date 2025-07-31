<?php

namespace Database\Seeders\Exam;

use App\Models\Exam\Exam;
use Illuminate\Database\Seeder;

class ExamSeeder extends Seeder
{
    public function run()
    {
        Exam::factory()->count(10)->create();
    }
}
