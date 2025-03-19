@extends('layouts.app')

@section('title', $burger->name)

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>{{ $burger->name }}</h1>
        <a href="{{ route('catalog.index') }}" class="btn btn-primary">
            <i class="fas fa-arrow-left me-1"></i> Retour au catalogue
        </a>
    </div>

    <div class="row">
        <div class="col-md-5 mb-4">
            <div class="card">
                <div class="card-body text-center">
                    @if($burger->image)
                        <img src="{{ asset('storage/' . $burger->image) }}" alt="{{ $burger->name }}" class="img-fluid rounded mb-3" style="max-height: 300px;">
                    @else
                        <div class="bg-secondary d-flex align-items-center justify-content-center rounded mb-3" style="height: 300px;">
                            <i class="fas fa-hamburger fa-5x text-white"></i>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-md-7">
            <div class="card">
                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0">Informations du burger</h5>
                </div>
                <div class="card-body">
                    <p class="fs-4 fw-bold text-primary">{{ number_format($burger->price, 0, ',', ' ') }} FCFA</p>
                    
                    <div class="mb-3">
                        <h5>Description</h5>
                        <p>{{ $burger->description ?: 'Aucune description disponible' }}</p>
                    </div>
                    
                    <div class="mb-4">
                        <h5>Disponibilité</h5>
                        @if($burger->isInStock())
                            <p class="text-success">
                                <i class="fas fa-check-circle me-1"></i> En stock ({{ $burger->stock }} disponibles)
                            </p>
                        @else
                            <p class="text-danger">
                                <i class="fas fa-times-circle me-1"></i> Indisponible
                            </p>
                        @endif
                    </div>
                    
                    @if($burger->isInStock())
                        <form action="{{ route('cart.add', $burger) }}" method="POST">
                            @csrf
                            <div class="row g-3 align-items-center">
                                <div class="col-auto">
                                    <label for="quantity" class="col-form-label">Quantité</label>
                                </div>
                                <div class="col-auto">
                                    <input type="number" id="quantity" name="quantity" class="form-control" value="1" min="1" max="{{ $burger->stock }}">
                                </div>
                                <div class="col-auto">
                                    <button type="submit" class="btn btn-success">
                                        <i class="fas fa-cart-plus me-1"></i> Ajouter au panier
                                    </button>
                                </div>
                            </div>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection 