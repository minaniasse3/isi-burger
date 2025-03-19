@extends('layouts.app')

@section('title', 'Commande #' . $order->id)

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Commande #{{ $order->id }}</h1>
        <div>
            @if(Auth::user()->isManager())
                <a href="{{ route('orders.index') }}" class="btn btn-primary">
                    <i class="fas fa-arrow-left me-1"></i> Retour aux commandes
                </a>
            @else
                <a href="{{ route('orders.index') }}" class="btn btn-primary">
                    <i class="fas fa-arrow-left me-1"></i> Retour à mes commandes
                </a>
            @endif
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Détails de la commande</h5>
                    <span class="badge 
                        @if($order->status === 'en_attente') bg-warning text-dark
                        @elseif($order->status === 'en_preparation') bg-info
                        @elseif($order->status === 'prete') bg-success
                        @elseif($order->status === 'payee') bg-primary
                        @elseif($order->status === 'annulee') bg-danger
                        @endif">
                        {{ $order->status_label }}
                    </span>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <h6>Date de commande :</h6>
                            <p>{{ $order->created_at->format('d/m/Y H:i') }}</p>
                        </div>
                        <div class="col-md-6">
                            <h6>Méthode de paiement :</h6>
                            <p>
                                @if($order->payment_method === 'especes')
                                    <i class="fas fa-money-bill-wave me-1"></i> Espèces
                                @elseif($order->payment_method === 'carte')
                                    <i class="fas fa-credit-card me-1"></i> Carte bancaire
                                @else
                                    {{ $order->payment_method }}
                                @endif
                            </p>
                        </div>
                    </div>

                    @if($order->isPaid())
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <h6>Date de paiement :</h6>
                                <p>{{ $order->payment_date->format('d/m/Y H:i') }}</p>
                            </div>
                            <div class="col-md-6">
                                <h6>Montant payé :</h6>
                                <p>
                                    {{ number_format($order->payment_amount, 0, ',', ' ') }} FCFA
                                    <a href="{{ route('payments.receipt', $order) }}" class="btn btn-sm btn-outline-primary ms-2">
                                        <i class="fas fa-receipt me-1"></i> Voir le reçu
                                    </a>
                                </p>
                            </div>
                        </div>
                    @endif

                    @if($order->notes)
                        <div class="mb-3">
                            <h6>Notes :</h6>
                            <p>{{ $order->notes }}</p>
                        </div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Produit</th>
                                    <th>Prix unitaire</th>
                                    <th>Quantité</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($order->items as $item)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                @if($item->burger->image)
                                                    <img src="{{ asset('storage/' . $item->burger->image) }}" alt="{{ $item->burger->name }}" class="me-3" style="width: 50px; height: 50px; object-fit: cover;">
                                                @else
                                                    <div class="bg-secondary d-flex align-items-center justify-content-center me-3" style="width: 50px; height: 50px;">
                                                        <i class="fas fa-hamburger text-white"></i>
                                                    </div>
                                                @endif
                                                <div>
                                                    <h6 class="mb-0">{{ $item->burger->name }}</h6>
                                                </div>
                                            </div>
                                        </td>
                                        <td>{{ number_format($item->unit_price, 0, ',', ' ') }} FCFA</td>
                                        <td>{{ $item->quantity }}</td>
                                        <td>{{ number_format($item->subtotal, 0, ',', ' ') }} FCFA</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="3" class="text-end fw-bold">Total :</td>
                                    <td class="fw-bold">{{ number_format($order->total_amount, 0, ',', ' ') }} FCFA</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>

            @if($order->canBeCancelled())
                <form action="{{ route('orders.cancel', $order) }}" method="POST" class="mb-4">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="btn btn-danger" onclick="return confirm('Êtes-vous sûr de vouloir annuler cette commande?')">
                        <i class="fas fa-times me-1"></i> Annuler la commande
                    </button>
                </form>
            @endif
        </div>

        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0">Informations client</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <h6>Nom :</h6>
                        <p>{{ $order->user->name }}</p>
                    </div>
                    <div class="mb-3">
                        <h6>Email :</h6>
                        <p>{{ $order->user->email }}</p>
                    </div>
                    <div class="mb-3">
                        <h6>Téléphone :</h6>
                        <p>{{ $order->user->phone ?: 'Non spécifié' }}</p>
                    </div>
                    <div class="mb-3">
                        <h6>Adresse :</h6>
                        <p>{{ $order->user->address ?: 'Non spécifiée' }}</p>
                    </div>
                </div>
            </div>

            @if(Auth::user()->isManager() && !$order->isCancelled())
                <div class="card">
                    <div class="card-header bg-dark text-white">
                        <h5 class="mb-0">Gestion de la commande</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('orders.updateStatus', $order) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <div class="mb-3">
                                <label for="status" class="form-label">Changer le statut</label>
                                <select class="form-select" id="status" name="status">
                                    <option value="en_attente" {{ $order->status === 'en_attente' ? 'selected' : '' }}>En attente</option>
                                    <option value="en_preparation" {{ $order->status === 'en_preparation' ? 'selected' : '' }}>En préparation</option>
                                    <option value="prete" {{ $order->status === 'prete' ? 'selected' : '' }}>Prête</option>
                                    <option value="payee" {{ $order->status === 'payee' ? 'selected' : '' }}>Payée</option>
                                </select>
                            </div>
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-1"></i> Mettre à jour le statut
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection 