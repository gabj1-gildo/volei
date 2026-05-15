<?php

namespace App\Http\Requests;

use App\Enums\StatusJogo;
use Illuminate\Foundation\Http\FormRequest;

class SalvarJogoRequest extends FormRequest
{
    /**
     * Apenas organizadores e admins podem criar jogos.
     */
    public function authorize(): bool
    {
        return in_array($this->user()?->tipo, ['admin', 'organizador']);
    }

    public function rules(): array
    {
        return [
            'titulo'                 => 'required|exists:titulos,id',
            'local'                  => 'required|exists:locais,id',
            'data'                   => 'required|date|after_or_equal:today',
            'hora'                   => 'required',
            'data_limite_inscricao'  => 'required|date|after_or_equal:today|before_or_equal:data',
            'hora_limite_inscricao'  => 'required',
            'limite_jogadores'       => 'required|integer|min:1',
            'descricao'              => 'nullable|string|max:1000',
            'responsavel_id'         => 'nullable|exists:users,id',
        ];
    }

    public function messages(): array
    {
        return [
            'titulo.required'                    => 'O título é obrigatório.',
            'titulo.exists'                      => 'Título selecionado não existe.',
            'local.required'                     => 'O local é obrigatório.',
            'local.exists'                       => 'Local selecionado não existe.',
            'data.required'                      => 'A data do jogo é obrigatória.',
            'data.after_or_equal'                => 'O jogo não pode ser marcado para uma data passada.',
            'data_limite_inscricao.before_or_equal' => 'A inscrição deve encerrar antes ou no dia do jogo.',
            'data_limite_inscricao.after_or_equal'  => 'A data limite de inscrição não pode ser no passado.',
            'limite_jogadores.min'               => 'O limite mínimo de jogadores é 1.',
            'descricao.max'                      => 'A descrição não pode ultrapassar 1000 caracteres.',
        ];
    }
}
