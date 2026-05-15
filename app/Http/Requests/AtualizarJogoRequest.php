<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AtualizarJogoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return in_array($this->user()?->tipo, ['admin', 'organizador']);
    }

    public function rules(): array
    {
        return [
            'id'                     => 'required|exists:jogos,id',
            'titulo'                 => 'required|exists:titulos,id',
            'local'                  => 'required|exists:locais,id',
            'data'                   => 'required|date',
            'hora'                   => 'required',
            'limite_jogadores'       => 'required|integer|min:1',
            'data_limite_inscricao'  => 'required|date',
            'hora_limite_inscricao'  => 'required',
            'descricao'              => 'nullable|string|max:1000',
            'responsavel_id'         => 'nullable|exists:users,id',
        ];
    }

    public function messages(): array
    {
        return [
            'id.exists'                       => 'Jogo não encontrado.',
            'titulo.required'                 => 'O título é obrigatório.',
            'local.required'                  => 'O local é obrigatório.',
            'data.required'                   => 'A data é obrigatória.',
            'limite_jogadores.min'            => 'O limite mínimo é 1 jogador.',
            'data_limite_inscricao.required'  => 'A data limite de inscrição é obrigatória.',
            'descricao.max'                   => 'A descrição não pode ultrapassar 1000 caracteres.',
        ];
    }
}
