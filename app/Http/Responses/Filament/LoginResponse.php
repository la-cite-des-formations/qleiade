<?php

namespace App\Http\Responses\Filament;

use Filament\Auth\Http\Responses\Contracts\LoginResponse as Responsable;
use Illuminate\Http\RedirectResponse;
use Livewire\Features\SupportRedirects\Redirector;

class LoginResponse implements Responsable
{
    /**
     * @param  \Illuminate\Http\Request  $request
     */
    public function toResponse($request): RedirectResponse | Redirector
    {
        $user = $request->user();

        if ($user->can('public_home')) {
            return redirect()->to('/home');
        }

        if ($user->can('public_admin')) {
            return redirect()->to('/admin');
        }

        // Cas 3: Ni home ni admin, on redirige vers l'erreur 403
        return redirect()->to('/access-denied');
    }
}
