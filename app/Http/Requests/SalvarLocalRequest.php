<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SalvarLocalRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->tipo === 'admin';
    }

    public function rules(): array
    {
        return [
            'id'       => 'sometimes|exists:locais,id',
            'nome'     => 'required|max:255',
            'endereco' => 'required|max:255',
            'tipo'     => 'required|in:publico,privado',
        ];
    }

    public function messages(): array
    {
        return [
            'nome.required'     => 'O nome do local é obrigatório.',
            'endereco.required' => 'O endereço é obrigatório.',
            'tipo.in'           => 'O tipo deve ser "público" ou "privado".',
        ];
    }
}
