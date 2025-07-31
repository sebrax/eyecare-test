<?php

namespace App\Services\Exam;

use App\Enums\Exam\LateralityEnum;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Collection;

class ExamPdfService
{
    /**
     * Prepara os exames agrupados pela opção de impressão.
     *
     * @param array $groups
     * @return array
     */
    public function prepareGroupedByPrintOption(array $groups): array
    {
        $allExams = collect($groups)->flatMap(function ($group) {
            return collect($group['exams'])->map(function ($exam) use ($group) {
                $laterality = $exam['laterality'] ?? null;

                if ($laterality && LateralityEnum::tryFrom($laterality)) {
                    $exam['laterality'] = LateralityEnum::from($laterality)->description();
                }
                return [
                    'groupTitle' => $group['title'],
                    'observation' => $group['observation'] ?? '',
                    'printOption' => $exam['printOption'] ?? $group['printOption'] ?? null,
                    'exam' => $exam,
                ];
            });
        });

        // Agrupa por printOption
        $groupedByPrintOption = $allExams->groupBy('printOption')->map(function ($items) {
            // Dentro do printOption, agrupa por observação
            $groupedByObservation = $items->groupBy('observation')->map(function ($obsItems, $observation) {
                return [
                    'observation' => $observation,
                    'exams' => $obsItems->pluck('exam')->all(),
                ];
            })->values()->all();

            return [
                'printOption' => $items->first()['printOption'],
                'groups' => $groupedByObservation,
            ];
        })->values()->all();

        return $groupedByPrintOption;
    }

    /**
     * Gera o PDF a partir da lista de exames.
     *
     * @param Collection $exams
     * @return \Barryvdh\DomPDF\PDF
     */
    public function generatePdf(array $groups)
    {
        $preparedGroups = $this->prepareGroupedByPrintOption($groups);

        $pdf = Pdf::loadView('pdf.exams', [
            'groupedExams' => $preparedGroups,
            'multipleGroups' => count($preparedGroups) > 1,
            'requesterName' => 'Isabella Santos de Souza',
            'patientName' => 'Maria Rosa Santos de Souza',
            'patientCpf' => '000.000.000-00',
            'doctorName' => 'Dra. Isabella Santos de Souza',
            'location' => 'São Paulo - SP',
            'patientBirthDate' => '01/01/1990',
            'patientGender' => 'Feminino',
            'patientPhone' => '(11) 99999-9999',
        ]);

        return $pdf;
    }
}
