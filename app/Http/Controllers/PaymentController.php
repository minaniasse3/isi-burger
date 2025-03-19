<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    /**
     * Afficher la liste des commandes prêtes pour paiement.
     */
    public function index()
    {
        // Vérifier que l'utilisateur est un gestionnaire
        if (!Auth::user()->isManager()) {
            return redirect()->route('catalog.index')
                ->with('error', 'Vous n\'avez pas les autorisations nécessaires pour accéder à cette page.');
        }

        // Récupérer les commandes prêtes (non payées et non annulées)
        $orders = Order::whereIn('status', ['prete'])
                      ->orderBy('created_at', 'desc')
                      ->get();

        return view('payments.index', compact('orders'));
    }

    /**
     * Afficher le formulaire de paiement pour une commande spécifique.
     */
    public function show(Order $order)
    {
        // Vérifier que l'utilisateur est un gestionnaire
        if (!Auth::user()->isManager()) {
            return redirect()->route('catalog.index')
                ->with('error', 'Vous n\'avez pas les autorisations nécessaires pour accéder à cette page.');
        }

        // Vérifier que la commande est prête pour paiement
        if ($order->status !== 'prete') {
            return redirect()->route('payments.index')
                ->with('error', 'Cette commande n\'est pas prête pour paiement.');
        }

        return view('payments.show', compact('order'));
    }

    /**
     * Enregistrer le paiement d'une commande.
     */
    public function process(Request $request, Order $order)
    {
        // Vérifier que l'utilisateur est un gestionnaire
        if (!Auth::user()->isManager()) {
            return redirect()->route('catalog.index')
                ->with('error', 'Vous n\'avez pas les autorisations nécessaires pour effectuer cette action.');
        }

        // Valider les données du formulaire
        $validated = $request->validate([
            'payment_amount' => 'required|numeric|min:' . $order->total_amount,
            'payment_method' => 'required|in:especes,carte',
        ]);

        // Vérifier que la commande est prête pour paiement
        if ($order->status !== 'prete') {
            return redirect()->route('payments.index')
                ->with('error', 'Cette commande n\'est pas prête pour paiement.');
        }

        // Vérifier que la commande n'a pas déjà été payée
        if ($order->isPaid()) {
            return redirect()->route('payments.index')
                ->with('error', 'Cette commande a déjà été payée.');
        }

        DB::beginTransaction();

        try {
            // Mettre à jour le statut de la commande
            $order->status = 'payee';
            $order->payment_date = now();
            $order->payment_amount = $validated['payment_amount'];
            $order->payment_method = $validated['payment_method'];
            $order->save();

            DB::commit();

            return redirect()->route('payments.index')
                ->with('success', 'Paiement enregistré avec succès.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('payments.show', $order)
                ->with('error', 'Une erreur est survenue lors de l\'enregistrement du paiement.');
        }
    }

    /**
     * Générer un reçu de paiement pour une commande.
     */
    public function receipt(Order $order)
    {
        // Vérifier que l'utilisateur est un gestionnaire ou le propriétaire de la commande
        if (!Auth::user()->isManager() && Auth::id() !== $order->user_id) {
            return redirect()->route('catalog.index')
                ->with('error', 'Vous n\'avez pas les autorisations nécessaires pour accéder à cette page.');
        }

        // Vérifier que la commande a été payée
        if (!$order->isPaid()) {
            return redirect()->back()
                ->with('error', 'Cette commande n\'a pas encore été payée.');
        }

        $order->load('items.burger', 'user');

        return view('payments.receipt', compact('order'));
    }
}
