@extends('layouts.app')

@section('title', 'Gestion des Paiements')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Gestion des Paiements</h1>
        <a href="{{ route('orders.index') }}" class="btn btn-primary">
            <i class="fas fa-arrow-left me-1"></i> Retour aux commandes
        </a>
    </div>

    @if($orders->isEmpty())
        <div class="alert alert-info">
            <p>Aucune commande n'est prête pour paiement.</p>
        </div>
    @else
        <div class="card">
            <div class="card-header bg-dark text-white">
                <h5 class="mb-0">Commandes prêtes pour paiement</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-striped table-hover mb-0">
                        <thead>
                            <tr>
                                <th>N° Commande</th>
                                <th>Client</th>
                                <th>Date</th>
                                <th>Montant</th>
                                <th>Statut</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($orders as $order)
                                <tr>
                                    <td>#{{ $order->id }}</td>
                                    <td>{{ $order->user->name }}</td>
                                    <td>{{ $order->created_at->format('d/m/Y H:i') }}</td>
                                    <td>{{ number_format($order->total_amount, 2) }} F CFA</td>
                                    <td>
                                        <span class="badge bg-success">{{ $order->status_label }}</span>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('orders.show', $order) }}" class="btn btn-sm btn-info text-white">
                                                <i class="fas fa-eye"></i> Détails
                                            </a>
                                            <a href="{{ route('payments.show', $order) }}" class="btn btn-sm btn-success">
                                                <i class="fas fa-money-bill-wave"></i> Encaisser
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif
@endsection 