<?php

namespace App\Enums\Exam;

/**
 * Enum que representa os grupos para impressão dos exames.
 */
enum GroupEnum: string
{
    case INDIVIDUAL = 'Individual';
    case GRUPO1 = 'Grupo 1';
    case GRUPO2 = 'Grupo 2';
    case GRUPO3 = 'Grupo 3';
    case GRUPO4 = 'Grupo 4';
    case GRUPO5 = 'Grupo 5';

    /**
     * Retorna um array associativo que mapeia os valores do enum
     * para suas descrições legíveis para humanos.
     *
     * @return array<string, string>
     */
    public static function descriptions(): array
    {
        return [
            self::INDIVIDUAL->value => 'Individual',
            self::GRUPO1->value => 'Grupo 1',
            self::GRUPO2->value => 'Grupo 2',
            self::GRUPO3->value => 'Grupo 3',
            self::GRUPO4->value => 'Grupo 4',
            self::GRUPO5->value => 'Grupo 5',
        ];
    }

    /**
     * Retorna a descrição legível para a instância atual do enum.
     * Caso não exista descrição, retorna o valor do enum como fallback.
     *
     * @return string
     */
    public function description(): string
    {
        return self::descriptions()[$this->value] ?? $this->value;
    }
}
