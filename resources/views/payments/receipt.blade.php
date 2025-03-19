@extends('layouts.app')

@section('title', 'Reçu de paiement - Commande #' . $order->id)

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Reçu de paiement</h1>
        <div>
            @if(Auth::user()->isManager())
                <a href="{{ route('payments.index') }}" class="btn btn-primary me-2">
                    <i class="fas fa-arrow-left me-1"></i> Retour aux paiements
                </a>
            @else
                <a href="{{ route('orders.index') }}" class="btn btn-primary me-2">
                    <i class="fas fa-arrow-left me-1"></i> Retour aux commandes
                </a>
            @endif
            <button class="btn btn-success" onclick="window.print()">
                <i class="fas fa-print me-1"></i> Imprimer
            </button>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="row mb-4">
                <div class="col-md-6">
                    <h2 class="mb-4">ISI BURGER</h2>
                    <p>VILLA 108 CITE GADAYE<br>
                    75000 SENEGAL<br>
                    Tél : 78 480 71 63<br>
                    Email : contact@isiburger.com</p>
                </div>
                <div class="col-md-6 text-md-end">
                    <h4 class="mb-4">Reçu de paiement</h4>
                    <p><strong>N° Commande :</strong> #{{ $order->id }}<br>
                    <strong>Date :</strong> {{ $order->payment_date->format('d/m/Y H:i') }}<br>
                    <strong>Client :</strong> {{ $order->user->name }}<br>
                    <strong>Adresse :</strong> {{ $order->user->address }}</p>
                </div>
            </div>

            <div class="table-responsive mb-4">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Produit</th>
                            <th class="text-end">Prix unitaire</th>
                            <th class="text-end">Quantité</th>
                            <th class="text-end">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($order->items as $item)
                            <tr>
                                <td>{{ $item->burger->name }}</td>
                                <td class="text-end">{{ number_format($item->unit_price, 0, ',', ' ') }} FCFA</td>
                                <td class="text-end">{{ $item->quantity }}</td>
                                <td class="text-end">{{ number_format($item->subtotal, 0, ',', ' ') }} FCFA</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="3" class="text-end fw-bold">Total :</td>
                            <td class="text-end fw-bold">{{ number_format($order->total_amount, 0, ',', ' ') }} FCFA</td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <h5>Informations de paiement</h5>
                    <p><strong>Méthode de paiement :</strong> 
                        @if($order->payment_method === 'especes')
                            Espèces
                        @elseif($order->payment_method === 'carte')
                            Carte bancaire
                        @else
                            {{ $order->payment_method }}
                        @endif
                        <br>
                        <strong>Montant payé :</strong> {{ number_format($order->payment_amount, 0, ',', ' ') }} FCFA<br>
                        <strong>Monnaie rendue :</strong> {{ number_format($order->payment_amount - $order->total_amount, 0, ',', ' ') }} FCFA
                    </p>
                </div>
                <div class="col-md-6 text-md-end">
                    <p class="mb-0 mt-4">Merci de votre confiance !</p>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<style>
    @media print {
        body * {
            visibility: hidden;
        }
        .card, .card * {
            visibility: visible;
        }
        .card {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
            border: none !important;
        }
        .btn, header, footer {
            display: none !important;
        }
    }
</style>
@endsection 