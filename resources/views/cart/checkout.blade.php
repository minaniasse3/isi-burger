@extends('layouts.app')

@section('title', 'Finaliser la commande')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Finaliser la commande</h1>
        <a href="{{ route('cart.index') }}" class="btn btn-primary">
            <i class="fas fa-arrow-left me-1"></i> Retour au panier
        </a>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0">Informations de livraison</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <h6>Nom :</h6>
                        <p class="mb-0">{{ Auth::user()->name }}</p>
                    </div>
                    <div class="mb-3">
                        <h6>Adresse de livraison :</h6>
                        <p class="mb-0">{{ Auth::user()->address ?: 'Non spécifiée' }}</p>
                    </div>
                    <div class="mb-3">
                        <h6>Téléphone :</h6>
                        <p class="mb-0">{{ Auth::user()->phone ?: 'Non spécifié' }}</p>
                    </div>
                    <div class="mb-3">
                        <h6>Email :</h6>
                        <p class="mb-0">{{ Auth::user()->email }}</p>
                    </div>

                    @if(!Auth::user()->address || !Auth::user()->phone)
                        <div class="alert alert-warning">
                            <p>Veuillez <a href="{{ route('profile') }}">compléter votre profil</a> avec votre adresse et numéro de téléphone avant de passer commande.</p>
                        </div>
                    @endif
                </div>
            </div>

            <div class="card">
                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0">Méthode de paiement</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('orders.store') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Choisissez votre méthode de paiement</label>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="payment_method" id="payment_cash" value="especes" checked>
                                <label class="form-check-label" for="payment_cash">
                                    <i class="fas fa-money-bill-wave me-2"></i> Paiement en espèces à la livraison
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="payment_method" id="payment_card" value="carte">
                                <label class="form-check-label" for="payment_card">
                                    <i class="fas fa-credit-card me-2"></i> Paiement par carte à la livraison
                                </label>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="notes" class="form-label">Notes supplémentaires</label>
                            <textarea class="form-control" id="notes" name="notes" rows="3" placeholder="Instructions spéciales pour la livraison..."></textarea>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-success" {{ (!Auth::user()->address || !Auth::user()->phone) ? 'disabled' : '' }}>
                                <i class="fas fa-check-circle me-1"></i> Confirmer la commande
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0">Récapitulatif de la commande</h5>
                </div>
                <div class="card-body">
                    @php
                        $cart = session()->get('cart', []);
                        $total = 0;
                    @endphp

                    @foreach($cart as $id => $details)
                        @php
                            $burger = App\Models\Burger::find($id);
                            if ($burger) {
                                $subtotal = $burger->price * $details['quantity'];
                                $total += $subtotal;
                            }
                        @endphp

                        @if($burger)
                            <div class="d-flex justify-content-between mb-2">
                                <div>
                                    <span class="fw-bold">{{ $burger->name }}</span>
                                    <small class="d-block text-muted">{{ $details['quantity'] }} x {{ number_format($burger->price, 0, ',', ' ') }} FCFA</small>
                                </div>
                                <span>{{ number_format($subtotal, 0, ',', ' ') }} FCFA</span>
                            </div>
                        @endif
                    @endforeach

                    <hr>

                    <div class="d-flex justify-content-between mb-2">
                        <span class="fw-bold">Total</span>
                        <span class="fw-bold">{{ number_format($total, 0, ',', ' ') }} FCFA</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection 