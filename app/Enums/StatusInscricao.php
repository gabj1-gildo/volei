<?php

namespace App\Enums;

enum StatusInscricao: string
{
    case Pendente   = 'pendente';
    case Confirmado = 'confirmado';
    case Recusada   = 'recusada';
    case Cancelada  = 'cancelada';

    /** Label legível para exibição em views. */
    public function label(): string
    {
        return match ($this) {
            self::Pendente   => 'Pendente',
            self::Confirmado => 'Confirmado',
            self::Recusada   => 'Recusada',
            self::Cancelada  => 'Cancelada',
        };
    }

    /** Retorna todos os valores string (útil para validação). */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    /** Indica se o status conta como vaga ocupada. */
    public function ocupaVaga(): bool
    {
        return match ($this) {
            self::Pendente, self::Confirmado => true,
            self::Recusada, self::Cancelada  => false,
        };
    }
}
