<?php

namespace App\Patterns\Creational;

use App\Enums\StatusJogo;
use App\Models\Jogo;
use Carbon\Carbon;

/**
 * Padrão Builder
 *
 * Constrói um objeto Jogo passo a passo, eliminando o array literal
 * Jogo::create([...]) nos controllers e centralizando as transformações
 * de dados (ex.: composição de data+hora com Carbon).
 */
class JogoBuilder
{
    private array $atributos = [];

    public function setTitulo(int $tituloId): static
    {
        $this->atributos['titulo_id'] = $tituloId;
        return $this;
    }

    public function setLocal(int $localId): static
    {
        $this->atributos['local_id'] = $localId;
        return $this;
    }

    /**
     * Recebe data (Y-m-d) e hora (H:i) e compõe o Carbon internamente.
     */
    public function setDataHora(string $data, string $hora): static
    {
        $this->atributos['data_hora'] = Carbon::parse("{$data} {$hora}");
        return $this;
    }

    /**
     * Recebe data e hora da inscrição e compõe o Carbon internamente.
     */
    public function setDataHoraLimiteInscricao(string $data, string $hora): static
    {
        $this->atributos['data_hora_limite_inscricao'] = Carbon::parse("{$data} {$hora}");
        return $this;
    }

    public function setLimiteJogadores(int $limite): static
    {
        $this->atributos['limite_jogadores'] = $limite;
        return $this;
    }

    public function setDescricao(?string $descricao): static
    {
        $this->atributos['descricao'] = $descricao;
        return $this;
    }

    public function setResponsavel(int $userId): static
    {
        $this->atributos['user_id'] = $userId;
        return $this;
    }

    public function setStatus(StatusJogo $status): static
    {
        $this->atributos['status'] = $status->value;
        return $this;
    }

    /**
     * Valida regras de negócio e persiste o Jogo no banco.
     *
     * @throws \InvalidArgumentException
     */
    public function build(): Jogo
    {
        $this->validar();
        return Jogo::create($this->atributos);
    }

    /**
     * Valida regras de negócio antes de persistir.
     *
     * @throws \InvalidArgumentException
     */
    private function validar(): void
    {
        $dataHoraJogo   = $this->atributos['data_hora'] ?? null;
        $dataHoraLimite = $this->atributos['data_hora_limite_inscricao'] ?? null;

        if ($dataHoraJogo === null || $dataHoraLimite === null) {
            throw new \InvalidArgumentException('Data/hora do jogo e do limite de inscrição são obrigatórios.');
        }

        if ($dataHoraJogo->isPast()) {
            throw new \InvalidArgumentException('O jogo não pode ser marcado para uma data/hora passada.');
        }

        if ($dataHoraLimite->gt($dataHoraJogo)) {
            throw new \InvalidArgumentException('As inscrições devem encerrar antes do início do jogo.');
        }

        if ($dataHoraLimite->isPast()) {
            throw new \InvalidArgumentException('A data limite de inscrição já passou.');
        }
    }
}
