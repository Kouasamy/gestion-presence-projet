<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IsAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Vérifie si l'utilisateur authentifié a le rôle d'administrateur
        if ($request->user() && $request->user()->role()->where('nom_role', 'admin')->exists()) {
            return $next($request);
        }

        // Sinon, redirige vers l'accueil ou page interdite
        return redirect('/')->with('error', 'Accès réservé à l\'administrateur');
    }
}
