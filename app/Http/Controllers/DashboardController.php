<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Afficher le tableau de bord en fonction du rôle de l'utilisateur.
     */
    public function index()
    {
        // Vérifier si l'utilisateur est connecté
        if (!Auth::check()) {
            return redirect()->route('catalog.index');
        }

        // Rediriger en fonction du rôle
        if (Auth::user()->isManager()) {
            return redirect()->route('stats.index');
        } else {
            return redirect()->route('orders.index');
        }
    }
}
