<?php

use App\Http\Controllers\Api\Exam\ExamController;
use App\Http\Controllers\Api\Package\PackageController;
use Illuminate\Support\Facades\Route;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Aqui definimos as rotas da API RESTful do sistema de exames e pacotes.
| Utilizamos versionamento via URL com prefixo 'v1' para permitir
| futuras evoluções da API sem quebrar clientes existentes.
|
*/

Route::prefix('v1')->group(function () {
    Route::get('exams/config', [ExamController::class, 'configData'])->name('exams.config');
    Route::post('exams/pdf/download', [ExamController::class, 'generatePdf'])->name('exams.generatePdf');
    Route::apiResource('exams', ExamController::class);

    Route::apiResource('packages', PackageController::class);
});
