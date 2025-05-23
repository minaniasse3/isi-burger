@extends('layouts.app')

@section('title', 'Mes Commandes')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Mes Commandes</h1>
        <a href="{{ route('catalog.index') }}" class="btn btn-primary">
            <i class="fas fa-hamburger me-1"></i> Retour au catalogue
        </a>
    </div>

    @if($orders->isEmpty())
        <div class="alert alert-info">
            <p>Vous n'avez pas encore passé de commande.</p>
            <a href="{{ route('catalog.index') }}" class="btn btn-primary mt-2">
                <i class="fas fa-hamburger me-1"></i> Découvrir nos burgers
            </a>
        </div>
    @else
        <div class="card">
            <div class="card-header bg-dark text-white">
                <h5 class="mb-0">Historique de mes commandes</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-striped table-hover mb-0">
                        <thead>
                            <tr>
                                <th>N° Commande</th>
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
                                    <td>{{ $order->created_at->format('d/m/Y H:i') }}</td>
                                    <td>{{ number_format($order->total_amount, 2) }} F CFA</td>
                                    <td>
                                        @if($order->status === 'en_attente')
                                            <span class="badge bg-warning text-dark">{{ $order->status_label }}</span>
                                        @elseif($order->status === 'en_preparation')
                                            <span class="badge bg-info">{{ $order->status_label }}</span>
                                        @elseif($order->status === 'prete')
                                            <span class="badge bg-success">{{ $order->status_label }}</span>
                                        @elseif($order->status === 'payee')
                                            <span class="badge bg-primary">{{ $order->status_label }}</span>
                                        @elseif($order->status === 'annulee')
                                            <span class="badge bg-danger">{{ $order->status_label }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('orders.show', $order) }}" class="btn btn-sm btn-info text-white">
                                                <i class="fas fa-eye"></i> Détails
                                            </a>
                                            
                                            @if($order->canBeCancelled())
                                                <form action="{{ route('orders.cancel', $order) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Êtes-vous sûr de vouloir annuler cette commande?')">
                                                        <i class="fas fa-times"></i> Annuler
                                                    </button>
                                                </form>
                                            @endif
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