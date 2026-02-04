<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Titulo; // Certifique-se de ter o Model criado

class TituloController extends Controller
{
    public function gerenciarTitulos()
    {
        $titulos = Titulo::all();
        return view('admin_gerenciar_titulos', compact('titulos'));
    }

    public function salvarTitulo(Request $request)
    {
        $request->validate([
            'nome' => 'required|string|max:255'
        ]);

        Titulo::create($request->all());

        return redirect()->back()->with('success', 'Título cadastrado com sucesso!');
    }

    public function atualizarTitulo(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:titulos,id',
            'nome' => 'required|string|max:255'
        ]);

        $titulo = Titulo::findOrFail($request->id);
        $titulo->update($request->only(['nome']));

        return redirect()->back()->with('success', 'Título atualizado com sucesso!');
    }
}