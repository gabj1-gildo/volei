<?php

namespace App\Enums;

enum StatusJogo: string
{
    case Aberto               = 'aberto';
    case InscricoesEncerradas = 'inscricoes_encerradas';
    case EmAndamento          = 'em_andamento';
    case Cancelado            = 'cancelado';
    case Encerrado            = 'encerrado';

    /**
     * Retorna os estados para os quais esta transição é permitida.
     *
     * @return StatusJogo[]
     */
    public function transicoesPermitidas(): array
    {
        return match ($this) {
            self::Aberto               => [self::InscricoesEncerradas, self::Cancelado],
            self::InscricoesEncerradas => [self::EmAndamento, self::Cancelado],
            self::EmAndamento          => [self::Encerrado, self::Cancelado],
            self::Cancelado            => [],
            self::Encerrado            => [],
        };
    }

    /** Label legível para exibição em views. */
    public function label(): string
    {
        return match ($this) {
            self::Aberto               => 'Aberto',
            self::InscricoesEncerradas => 'Inscrições Encerradas',
            self::EmAndamento          => 'Em Andamento',
            self::Cancelado            => 'Cancelado',
            self::Encerrado            => 'Encerrado',
        };
    }

    /** Retorna todos os valores string (útil para validação). */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
