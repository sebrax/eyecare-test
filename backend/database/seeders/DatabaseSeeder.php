<?php

namespace Database\Seeders;

use Database\Seeders\Exam\ExamSeeder;
use Database\Seeders\Package\PackageSeeder;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $this->call([
            ExamSeeder::class,
            PackageSeeder::class
        ]);
    }
}
