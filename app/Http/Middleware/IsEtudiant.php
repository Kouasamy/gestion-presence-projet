<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IsEtudiant
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
       if ($request->user() && $request->user()->role()->where('nom_role', 'etudiant')->exists()) {
            return $next($request);
        }

        return redirect('/')->with('error', 'Accès réservé aux étudiants');
    }
}
