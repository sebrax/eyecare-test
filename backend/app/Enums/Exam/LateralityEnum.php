<?php

namespace App\Enums\Exam;

/**
 * Enum que representa as lateralidades possíveis para os exames.
 */
enum LateralityEnum: string
{
    case OD = 'OD';
    case OE = 'OE';
    case AO = 'AO';

    /**
     * Retorna um array associativo que mapeia os valores do enum
     * para suas descrições legíveis para humanos.
     */
    public static function descriptions(): array
    {
        return [
            self::OD->value => 'Olho Direito',
            self::OE->value => 'Olho Esquerdo',
            self::AO->value => 'Ambos os Olhos',
        ];
    }

    /**
     * Retorna a descrição legível para a instância atual do enum, usando o valor do enum como fallback caso não exista descrição.
     *
     * @return string
     */
    public function description(): string
    {
        return self::descriptions()[$this->value] ?? $this->value;
    }
}
