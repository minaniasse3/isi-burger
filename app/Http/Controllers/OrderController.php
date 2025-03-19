<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Burger;
use App\Models\User;
use App\Notifications\OrderCreated;
use App\Notifications\NewOrderForManager;
use App\Notifications\OrderReady;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Barryvdh\DomPDF\Facade\Pdf;

class OrderController extends Controller
{
    /**
     * Display a listing of the orders.
     */
    public function index()
    {
        if (Auth::user()->isManager()) {
            // Les gestionnaires voient toutes les commandes
            $orders = Order::with('user')->orderBy('created_at', 'desc')->get();
            return view('orders.index', compact('orders'));
        } else {
            // Les clients ne voient que leurs propres commandes
            $orders = Auth::user()->orders()->orderBy('created_at', 'desc')->get();
            return view('orders.my_orders', compact('orders'));
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created order in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'payment_method' => 'required|string|in:especes,carte',
            'notes' => 'nullable|string|max:500',
        ]);

        $cart = session()->get('cart', []);
        
        if (empty($cart)) {
            return redirect()->route('cart.index')->with('error', 'Votre panier est vide.');
        }

        // Vérifier la disponibilité des produits
        $outOfStock = false;
        $totalAmount = 0;
        $orderItems = [];

        DB::beginTransaction();

        try {
            foreach ($cart as $id => $details) {
                $burger = Burger::find($id);
                
                if (!$burger || !$burger->isInStock() || $burger->stock < $details['quantity']) {
                    $outOfStock = true;
                    break;
                }

                $subtotal = $burger->price * $details['quantity'];
                $totalAmount += $subtotal;

                $orderItems[] = [
                    'burger_id' => $burger->id,
                    'quantity' => $details['quantity'],
                    'unit_price' => $burger->price,
                    'subtotal' => $subtotal,
                ];

                // Mettre à jour le stock
                $burger->stock -= $details['quantity'];
                $burger->is_available = $burger->stock > 0;
                $burger->save();
            }

            if ($outOfStock) {
                DB::rollBack();
                return redirect()->route('cart.index')->with('error', 'Certains produits ne sont plus disponibles en quantité suffisante.');
            }

            // Créer la commande
            $order = Order::create([
                'user_id' => Auth::id(),
                'total_amount' => $totalAmount,
                'status' => 'en_attente',
                'payment_method' => $request->payment_method,
                'notes' => $request->notes,
            ]);

            // Créer les éléments de commande
            foreach ($orderItems as $item) {
                $order->items()->create($item);
            }

            DB::commit();

            // Envoyer une notification au client
            $order->user->notify(new OrderCreated($order));

            // Envoyer une notification aux gestionnaires
            $managers = User::where('role', 'gestionnaire')->get();
            Notification::send($managers, new NewOrderForManager($order));

            // Vider le panier
            session()->forget('cart');

            return redirect()->route('orders.show', $order)->with('success', 'Votre commande a été passée avec succès. Un email de confirmation vous a été envoyé.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('cart.checkout')->with('error', 'Une erreur est survenue lors de la création de votre commande. Veuillez réessayer.');
        }
    }

    /**
     * Display the specified order.
     */
    public function show(Order $order)
    {
        // Vérifier que l'utilisateur a le droit de voir cette commande
        if (!Auth::user()->isManager() && Auth::id() !== $order->user_id) {
            return redirect()->route('orders.index')->with('error', 'Vous n\'êtes pas autorisé à voir cette commande.');
        }

        $order->load('items.burger', 'user');
        return view('orders.show', compact('order'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    /**
     * Cancel the specified order.
     */
    public function cancel(Order $order)
    {
        // Vérifier que l'utilisateur a le droit d'annuler cette commande
        if (!Auth::user()->isManager() && Auth::id() !== $order->user_id) {
            return redirect()->route('orders.index')->with('error', 'Vous n\'êtes pas autorisé à annuler cette commande.');
        }

        // Vérifier que la commande peut être annulée
        if (!$order->canBeCancelled()) {
            return redirect()->route('orders.show', $order)->with('error', 'Cette commande ne peut plus être annulée.');
        }

        DB::beginTransaction();

        try {
            // Remettre les produits en stock
            foreach ($order->items as $item) {
                $burger = $item->burger;
                $burger->stock += $item->quantity;
                $burger->is_available = true;
                $burger->save();
            }

            // Mettre à jour le statut de la commande
            $order->status = 'annulee';
            $order->save();

            DB::commit();

            return redirect()->route('orders.show', $order)->with('success', 'La commande a été annulée avec succès.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('orders.show', $order)->with('error', 'Une erreur est survenue lors de l\'annulation de la commande.');
        }
    }

    /**
     * Update the status of the specified order.
     */
    public function updateStatus(Request $request, Order $order)
    {
        // Vérifier que l'utilisateur est un gestionnaire
        if (!Auth::user()->isManager()) {
            return redirect()->route('orders.index')->with('error', 'Vous n\'êtes pas autorisé à modifier le statut de cette commande.');
        }

        $request->validate([
            'status' => 'required|string|in:en_attente,en_preparation,prete,payee',
        ]);

        $oldStatus = $order->status;
        $newStatus = $request->status;

        // Mettre à jour le statut
        $order->status = $newStatus;

        // Si la commande est payée, enregistrer la date et le montant du paiement
        if ($newStatus === 'payee' && $oldStatus !== 'payee') {
            $order->payment_date = now();
            $order->payment_amount = $order->total_amount;
        }

        $order->save();

        // Si la commande est prête, envoyer un e-mail avec la facture
        if ($newStatus === 'prete' && $oldStatus !== 'prete') {
            // Charger les relations nécessaires
            $order->load('items.burger', 'user');
            
            // Envoyer la notification avec la facture
            $order->user->notify(new OrderReady($order));
            
            // Rediriger vers la page de paiement
            return redirect()->route('payments.show', $order)->with('success', 'La commande est prête pour paiement. Un email avec la facture a été envoyé au client.');
        }

        return redirect()->route('orders.show', $order)->with('success', 'Le statut de la commande a été mis à jour avec succès.');
    }
}
