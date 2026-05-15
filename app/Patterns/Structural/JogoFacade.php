<?php

namespace App\Patterns\Structural;

use App\Enums\StatusJogo;
use App\Http\Requests\AtualizarJogoRequest;
use App\Http\Requests\SalvarJogoRequest;
use App\Models\Jogo;
use App\Patterns\Behavioral\State\JogoContext;
use App\Patterns\Creational\JogoBuilder;
use App\Patterns\Creational\SessionManager;

/**
 * Padrão Facade
 *
 * Fornece uma interface simplificada para operações de Jogo.
 * Orquestra internamente: JogoBuilder (criação), JogoContext (transição de estado)
 * e SessionManager (identidade do responsável).
 *
 * Controllers dependem apenas desta classe, sem conhecer os subsistemas internos.
 */
class JogoFacade
{
    /**
     * Cria um novo Jogo a partir dos dados validados do Form Request.
     *
     * @throws \InvalidArgumentException Quando as datas violam regras de negócio.
     */
    public static function criarJogo(SalvarJogoRequest $request): Jogo
    {
        $session = SessionManager::getInstance();

        // Determina o responsável: admin pode escolher outro organizador
        $responsavelId = ($session->userIs('admin') && $request->filled('responsavel_id'))
            ? (int) $request->responsavel_id
            : $session->getUserId();

        return (new JogoBuilder())
            ->setTitulo((int) $request->titulo)
            ->setLocal((int) $request->local)
            ->setDataHora($request->data, $request->hora)
            ->setDataHoraLimiteInscricao($request->data_limite_inscricao, $request->hora_limite_inscricao)
            ->setLimiteJogadores((int) $request->limite_jogadores)
            ->setDescricao($request->descricao)
            ->setResponsavel($responsavelId)
            ->setStatus(StatusJogo::Aberto)
            ->build();
    }

    /**
     * Atualiza um Jogo existente com os dados validados do Form Request.
     *
     * @throws \InvalidArgumentException Quando as datas violam regras de negócio.
     */
    public static function atualizarJogo(AtualizarJogoRequest $request, Jogo $jogo): void
    {
        $dataHoraJogo   = \Carbon\Carbon::parse("{$request->data} {$request->hora}");
        $dataHoraLimite = \Carbon\Carbon::parse("{$request->data_limite_inscricao} {$request->hora_limite_inscricao}");

        if ($dataHoraLimite->gt($dataHoraJogo)) {
            throw new \InvalidArgumentException('A data limite de inscrição não pode ser após o início do jogo.');
        }

        if ($dataHoraJogo->isPast() && $jogo->data_hora != $dataHoraJogo) {
            throw new \InvalidArgumentException('Você não pode alterar um jogo para uma data que já passou.');
        }

        $session = SessionManager::getInstance();

        $jogo->update([
            'titulo_id'                  => $request->titulo,
            'local_id'                   => $request->local,
            'data_hora'                  => $dataHoraJogo,
            'limite_jogadores'           => $request->limite_jogadores,
            'data_hora_limite_inscricao' => $dataHoraLimite,
            'descricao'                  => $request->descricao,
            'user_id'                    => $request->filled('responsavel_id')
                                            ? $request->responsavel_id
                                            : $jogo->user_id,
        ]);
    }

    /**
     * Altera o status do Jogo usando o State Pattern.
     * Lança \LogicException se a transição não for permitida.
     *
     * @throws \LogicException
     */
    public static function alterarStatus(Jogo $jogo, string $novoStatus): void
    {
        $context = new JogoContext($jogo);

        match ($novoStatus) {
            StatusJogo::Aberto->value               => $context->abrir(),
            StatusJogo::InscricoesEncerradas->value => $context->encerrarInscricoes(),
            StatusJogo::EmAndamento->value          => $context->iniciar(),
            StatusJogo::Cancelado->value            => $context->cancelar(),
            StatusJogo::Encerrado->value            => $context->encerrar(),
            default => throw new \InvalidArgumentException("Status '{$novoStatus}' inválido."),
        };
    }
}
