<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Vérifie que l'utilisateur est authentifié et possède le rôle 'admin'.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user || $user->role !== 'admin') {
            return response()->json([
                'status' => 'error',
                'message' => 'Accès refusé. Espace réservé exclusivement aux administrateurs HorizonVols.'
            ], 403);
        }

        return $next($request);
    }
}