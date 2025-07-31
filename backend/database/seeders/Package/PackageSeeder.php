<?php

namespace Database\Seeders\Package;

use App\Models\Exam\Exam;
use App\Models\Package\Package;
use Illuminate\Database\Seeder;

class PackageSeeder extends Seeder
{
    public function run()
    {
        Package::factory()->count(5)->create()->each(function ($package) {
            $exams = Exam::inRandomOrder()->take(rand(2, 5))->pluck('id');
            $package->exams()->attach($exams);
        });
    }
}
