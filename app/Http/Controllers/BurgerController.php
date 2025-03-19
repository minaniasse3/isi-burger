<?php

namespace App\Http\Controllers;

use App\Models\Burger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class BurgerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $burgers = Burger::where('is_archived', false)->orderBy('name')->get();
        return view('burgers.index', compact('burgers'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('burgers.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:burgers',
            'price' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'stock' => 'required|integer|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('burgers', 'public');
            $validated['image'] = $imagePath;
        }

        Burger::create($validated);

        return redirect()->route('burgers.index')
            ->with('success', 'Burger créé avec succès.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Burger $burger)
    {
        return view('burgers.show', compact('burger'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Burger $burger)
    {
        return view('burgers.edit', compact('burger'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Burger $burger)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('burgers')->ignore($burger->id)],
            'price' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'stock' => 'required|integer|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($request->hasFile('image')) {
            // Supprimer l'ancienne image si elle existe
            if ($burger->image) {
                Storage::disk('public')->delete($burger->image);
            }
            
            $imagePath = $request->file('image')->store('burgers', 'public');
            $validated['image'] = $imagePath;
        }

        $burger->update($validated);

        return redirect()->route('burgers.index')
            ->with('success', 'Burger mis à jour avec succès.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Burger $burger)
    {
        // Suppression physique
        if ($burger->image) {
            Storage::disk('public')->delete($burger->image);
        }
        
        $burger->delete();

        return redirect()->route('burgers.index')
            ->with('success', 'Burger supprimé avec succès.');
    }
    
    /**
     * Archive the specified resource.
     */
    public function archive(Burger $burger)
    {
        $burger->update(['is_archived' => true]);

        return redirect()->route('burgers.index')
            ->with('success', 'Burger archivé avec succès.');
    }
    
    /**
     * Display a listing of archived resources.
     */
    public function archived()
    {
        $burgers = Burger::where('is_archived', true)->orderBy('name')->get();
        return view('burgers.archived', compact('burgers'));
    }
    
    /**
     * Restore the archived resource.
     */
    public function restore(Burger $burger)
    {
        $burger->update(['is_archived' => false]);

        return redirect()->route('burgers.archived')
            ->with('success', 'Burger restauré avec succès.');
    }
    
    /**
     * Update the stock of the specified resource.
     */
    public function updateStock(Request $request, Burger $burger)
    {
        $validated = $request->validate([
            'stock' => 'required|integer|min:0',
        ]);

        $burger->update([
            'stock' => $validated['stock'],
            'is_available' => $validated['stock'] > 0
        ]);

        return redirect()->route('burgers.index')
            ->with('success', 'Stock mis à jour avec succès.');
    }
}
