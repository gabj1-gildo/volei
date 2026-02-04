<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(Request $request)
    {
        $user = $request->user();

        // 1. Validação: Adicionei name e email para garantir que não sejam perdidos
        $validatedData = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email,'.$user->id],
            'username' => ['required', 'string', 'alpha_dash', 'min:3', 'max:20', 'unique:users,username,'.$user->id],
        ]);

        // 2. Regra dos 7 dias para o Username
        if ($request->username !== $user->username) {
            $ultimaAlteracao = $user->username_updated_at;

            // Verifica se o campo não é nulo e se a diferença é menor que 7 dias
            if ($ultimaAlteracao && $ultimaAlteracao->diffInDays(now()) < 7) {
                // Usamos copy() para não alterar o valor real no banco antes da hora
                $proximaData = $ultimaAlteracao->copy()->addDays(7)->format('d/m/Y');
                return back()->with('error', "Você só poderá alterar o nome de usuário novamente em {$proximaData}.");
            }

            $user->username_updated_at = now();
        }

        // 3. Persistência dos dados
        $user->fill($validatedData);

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        return redirect()->route('profile.edit')->with('status', 'profile-updated');
}

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
