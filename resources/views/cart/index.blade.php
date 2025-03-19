@extends('layouts.app')

@section('title', 'Mon Panier')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Mon Panier</h1>
        <a href="{{ route('catalog.index') }}" class="btn btn-primary">
            <i class="fas fa-arrow-left me-1"></i> Continuer mes achats
        </a>
    </div>

    @if(empty($items))
        <div class="alert alert-info">
            <p>Votre panier est vide.</p>
            <a href="{{ route('catalog.index') }}" class="btn btn-primary mt-2">
                <i class="fas fa-hamburger me-1"></i> Découvrir nos burgers
            </a>
        </div>
    @else
        <div class="card mb-4">
            <div class="card-header bg-dark text-white">
                <h5 class="mb-0">Articles dans votre panier</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-striped table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Produit</th>
                                <th>Prix unitaire</th>
                                <th>Quantité</th>
                                <th>Total</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($items as $item)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            @if($item['burger']->image)
                                                <img src="{{ asset('storage/' . $item['burger']->image) }}" alt="{{ $item['burger']->name }}" class="me-3" style="width: 50px; height: 50px; object-fit: cover;">
                                            @else
                                                <div class="bg-secondary d-flex align-items-center justify-content-center me-3" style="width: 50px; height: 50px;">
                                                    <i class="fas fa-hamburger text-white"></i>
                                                </div>
                                            @endif
                                            <div>
                                                <h6 class="mb-0">{{ $item['burger']->name }}</h6>
                                                <small class="text-muted">
                                                    @if($item['burger']->isInStock())
                                                        <span class="text-success">En stock</span>
                                                    @else
                                                        <span class="text-danger">Indisponible</span>
                                                    @endif
                                                </small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ number_format($item['burger']->price, 0, ',', ' ') }} FCFA</td>
                                    <td>
                                        <form action="{{ route('cart.update', $item['burger']->id) }}" method="POST" class="d-flex align-items-center">
                                            @csrf
                                            @method('PATCH')
                                            <input type="number" name="quantity" value="{{ $item['quantity'] }}" min="1" max="{{ $item['burger']->stock }}" class="form-control form-control-sm me-2" style="width: 70px;">
                                            <button type="submit" class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-sync-alt"></i>
                                            </button>
                                        </form>
                                    </td>
                                    <td>{{ number_format($item['burger']->price * $item['quantity'], 0, ',', ' ') }} FCFA</td>
                                    <td>
                                        <form action="{{ route('cart.remove', $item['burger']->id) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger">
                                                <i class="fas fa-trash"></i> Supprimer
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="table-dark">
                            <tr>
                                <td colspan="3" class="text-end fw-bold">Total :</td>
                                <td class="fw-bold">{{ number_format($total, 0, ',', ' ') }} FCFA</td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-between">
            <form action="{{ route('cart.clear') }}" method="POST">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-outline-danger">
                    <i class="fas fa-trash me-1"></i> Vider le panier
                </button>
            </form>

            <a href="{{ route('cart.checkout') }}" class="btn btn-success">
                <i class="fas fa-check-circle me-1"></i> Passer la commande
            </a>
        </div>
    @endif
@endsection 