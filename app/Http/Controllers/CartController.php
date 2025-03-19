<?php

namespace App\Http\Controllers;

use App\Models\Burger;
use Illuminate\Http\Request;

class CartController extends Controller
{
    /**
     * Display the cart contents.
     */
    public function index()
    {
        $cart = session()->get('cart', []);
        $total = 0;
        $items = [];

        foreach ($cart as $id => $details) {
            $burger = Burger::find($id);
            if ($burger) {
                $items[] = [
                    'burger' => $burger,
                    'quantity' => $details['quantity']
                ];
                $total += $burger->price * $details['quantity'];
            }
        }

        return view('cart.index', compact('items', 'total'));
    }

    /**
     * Add a burger to the cart.
     */
    public function add(Request $request, Burger $burger)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1|max:' . $burger->stock,
        ]);

        if (!$burger->isInStock()) {
            return redirect()->back()->with('error', 'Ce burger n\'est pas disponible actuellement.');
        }

        $cart = session()->get('cart', []);
        $quantity = $request->quantity;

        // Si le burger est déjà dans le panier, mettre à jour la quantité
        if (isset($cart[$burger->id])) {
            $cart[$burger->id]['quantity'] += $quantity;
        } else {
            $cart[$burger->id] = [
                'quantity' => $quantity
            ];
        }

        session()->put('cart', $cart);

        return redirect()->route('cart.index')->with('success', 'Burger ajouté au panier avec succès.');
    }

    /**
     * Update the quantity of a burger in the cart.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        $burger = Burger::findOrFail($id);
        
        if ($request->quantity > $burger->stock) {
            return redirect()->back()->with('error', 'La quantité demandée dépasse le stock disponible.');
        }

        $cart = session()->get('cart', []);

        if (isset($cart[$id])) {
            $cart[$id]['quantity'] = $request->quantity;
            session()->put('cart', $cart);
        }

        return redirect()->route('cart.index')->with('success', 'Panier mis à jour avec succès.');
    }

    /**
     * Remove a burger from the cart.
     */
    public function remove($id)
    {
        $cart = session()->get('cart', []);

        if (isset($cart[$id])) {
            unset($cart[$id]);
            session()->put('cart', $cart);
        }

        return redirect()->route('cart.index')->with('success', 'Produit retiré du panier avec succès.');
    }

    /**
     * Clear the entire cart.
     */
    public function clear()
    {
        session()->forget('cart');
        return redirect()->route('cart.index')->with('success', 'Panier vidé avec succès.');
    }

    /**
     * Proceed to checkout.
     */
    public function checkout()
    {
        $cart = session()->get('cart', []);
        
        if (empty($cart)) {
            return redirect()->route('cart.index')->with('error', 'Votre panier est vide.');
        }

        // Vérifier la disponibilité des produits
        $outOfStock = false;
        foreach ($cart as $id => $details) {
            $burger = Burger::find($id);
            if (!$burger || !$burger->isInStock() || $burger->stock < $details['quantity']) {
                $outOfStock = true;
                break;
            }
        }

        if ($outOfStock) {
            return redirect()->route('cart.index')->with('erreur', 'Certains produits ne sont plus disponibles en quantité suffisante.');
        }

        return view('cart.checkout');
    }
}
