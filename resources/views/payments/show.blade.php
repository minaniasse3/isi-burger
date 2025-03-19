@extends('layouts.app')

@section('title', 'Paiement de la commande #' . $order->id)

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Paiement de la commande #{{ $order->id }}</h1>
        <a href="{{ route('payments.index') }}" class="btn btn-primary">
            <i class="fas fa-arrow-left me-1"></i> Retour aux paiements
        </a>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0">Détails de la commande</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <h6>Client :</h6>
                            <p>{{ $order->user->name }}</p>
                        </div>
                        <div class="col-md-6">
                            <h6>Date de commande :</h6>
                            <p>{{ $order->created_at->format('d/m/Y H:i') }}</p>
                        </div>
                    </div>

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
                                        <td>{{ $item->burger->name }}</td>
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
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0">Enregistrer le paiement</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('payments.process', $order) }}" method="POST">
                        @csrf

                        <div class="mb-3">
                            <label for="payment_amount" class="form-label">Montant reçu (FCFA)</label>
                            <input type="number" step="0.01" min="{{ $order->total_amount }}" class="form-control @error('payment_amount') is-invalid @enderror" id="payment_amount" name="payment_amount" value="{{ old('payment_amount', $order->total_amount) }}" required>
                            @error('payment_amount')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                            <div class="form-text">Le montant minimum est de {{ number_format($order->total_amount, 0, ',', ' ') }} FCFA</div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Méthode de paiement</label>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="payment_method" id="payment_cash" value="especes" checked>
                                <label class="form-check-label" for="payment_cash">
                                    <i class="fas fa-money-bill-wave me-2"></i> Espèces
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="payment_method" id="payment_card" value="carte">
                                <label class="form-check-label" for="payment_card">
                                    <i class="fas fa-credit-card me-2"></i> Carte bancaire
                                </label>
                            </div>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-check-circle me-1"></i> Confirmer le paiement
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script>
    // Calculer la monnaie à rendre
    document.getElementById('payment_amount').addEventListener('input', function() {
        const totalAmount = {{ $order->total_amount }};
        const paymentAmount = parseFloat(this.value) || 0;
        const change = paymentAmount - totalAmount;
        
        if (change >= 0) {
            this.classList.remove('is-invalid');
            this.classList.add('is-valid');
        } else {
            this.classList.remove('is-valid');
            this.classList.add('is-invalid');
        }
    });
</script>
@endsection 