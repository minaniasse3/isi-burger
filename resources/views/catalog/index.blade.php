@extends('layouts.app')

@section('title', 'Catalogue des Burgers')

@section('content')
    <div class="row mb-4">
        <div class="col-md-8">
            <h1>Catalogue des Burgers</h1>
        </div>
        <div class="col-md-4">
            <form action="{{ route('catalog.index') }}" method="GET" class="d-flex">
                <input type="text" name="search" class="form-control me-2" placeholder="Rechercher..." value="{{ request('search') }}">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-search"></i>
                </button>
            </form>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0">Filtres</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('catalog.index') }}" method="GET" class="row g-3">
                        <div class="col-md-3">
                            <label for="min_price" class="form-label">Prix minimum</label>
                            <input type="number" step="0.01" min="0" class="form-control" id="min_price" name="min_price" value="{{ request('min_price') }}">
                        </div>
                        <div class="col-md-3">
                            <label for="max_price" class="form-label">Prix maximum</label>
                            <input type="number" step="0.01" min="0" class="form-control" id="max_price" name="max_price" value="{{ request('max_price') }}">
                        </div>
                        <div class="col-md-3">
                            <label for="sort_by" class="form-label">Trier par</label>
                            <select class="form-select" id="sort_by" name="sort_by">
                                <option value="name" {{ request('sort_by') == 'name' ? 'selected' : '' }}>Nom</option>
                                <option value="price" {{ request('sort_by') == 'price' ? 'selected' : '' }}>Prix</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="sort_order" class="form-label">Ordre</label>
                            <select class="form-select" id="sort_order" name="sort_order">
                                <option value="asc" {{ request('sort_order') == 'asc' ? 'selected' : '' }}>Croissant</option>
                                <option value="desc" {{ request('sort_order') == 'desc' ? 'selected' : '' }}>Décroissant</option>
                            </select>
                        </div>
                        <div class="col-12 text-end">
                            <a href="{{ route('catalog.index') }}" class="btn btn-secondary me-2">Réinitialiser</a>
                            <button type="submit" class="btn btn-primary">Appliquer les filtres</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @if($burgers->isEmpty())
        <div class="alert alert-info">
            Aucun burger ne correspond à vos critères de recherche.
        </div>
    @else
        <div class="row row-cols-1 row-cols-md-3 g-4">
            @foreach($burgers as $burger)
                <div class="col">
                    <div class="card h-100">
                        @if($burger->image)
                            <img src="{{ asset('storage/' . $burger->image) }}" class="card-img-top" alt="{{ $burger->name }}" style="height: 200px; object-fit: cover;">
                        @else
                            <div class="bg-secondary d-flex align-items-center justify-content-center" style="height: 200px;">
                                <i class="fas fa-hamburger fa-4x text-white"></i>
                            </div>
                        @endif
                        <div class="card-body">
                            <h5 class="card-title">{{ $burger->name }}</h5>
                            <p class="card-text">
                                <strong>Prix :</strong> {{ number_format($burger->price, 0, ',', ' ') }} FCFA
                            </p>
                            <p class="card-text">
                                {{ Str::limit($burger->description, 100) }}
                            </p>
                        </div>
                        <div class="card-footer d-flex justify-content-between align-items-center">
                            <a href="{{ route('catalog.show', $burger) }}" class="btn btn-primary">
                                <i class="fas fa-eye me-1"></i> Détails
                            </a>
                            @if($burger->isInStock())
                                <form action="{{ route('cart.add', $burger) }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="quantity" value="1">
                                    <button type="submit" class="btn btn-success">
                                        <i class="fas fa-cart-plus me-1"></i> Ajouter
                                    </button>
                                </form>
                            @else
                                <span class="badge bg-danger">Indisponible</span>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
@endsection 