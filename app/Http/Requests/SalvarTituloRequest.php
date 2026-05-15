<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SalvarTituloRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->tipo === 'admin';
    }

    public function rules(): array
    {
        return [
            'id'   => 'sometimes|exists:titulos,id',
            'nome' => 'required|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'nome.required' => 'O nome do título é obrigatório.',
            'nome.max'      => 'O nome não pode ultrapassar 255 caracteres.',
        ];
    }
}
