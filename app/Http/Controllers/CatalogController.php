<?php

namespace App\Http\Controllers;

use App\Models\Burger;
use Illuminate\Http\Request;

class CatalogController extends Controller
{
    /**
     * Display the catalog of burgers with optional filters.
     */
    public function index(Request $request)
{
    $query = Burger::where('is_archived', false)
                   ->where('is_available', true);

    // Filtrer par prix
    if ($request->has('min_price') && !is_null($request->min_price)) {
        $query->where('price', '>=', $request->min_price);
    }

    if ($request->has('max_price') && !is_null($request->max_price)) {
        $query->where('price', '<=', $request->max_price);
    }

    // Filtrer par nom
    if ($request->has('search')) {
        $query->where('name', 'like', '%' . $request->search . '%')
              ->orWhere('description', 'like', '%' . $request->search . '%');
    }

    // Trier les résultats
    $sortBy = $request->sort_by ?? 'name';
    $sortOrder = $request->sort_order ?? 'asc';

    $burgers = $query->orderBy($sortBy, $sortOrder)->get();

    return view('catalog.index', compact('burgers'));
}


    /**
     * Display the specified burger.
     */
    public function show(Burger $burger)
    {
        if ($burger->is_archived) {
            return redirect()->route('catalog.index')
                ->with('erreur', 'Ce burger n\'est plus disponible.');
        }

        return view('catalog.show', compact('burger'));
    }
}
