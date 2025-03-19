@extends('layouts.app')

@section('title', 'Statistiques journalières')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Statistiques journalières</h1>
        <div>
            <a href="{{ route('stats.index') }}" class="btn btn-outline-primary me-2">
                <i class="fas fa-chart-bar me-1"></i> Tableau de bord
            </a>
            <a href="{{ route('stats.sales') }}" class="btn btn-outline-primary me-2">
                <i class="fas fa-chart-line me-1"></i> Ventes
            </a>
            <a href="{{ route('stats.products') }}" class="btn btn-outline-primary me-2">
                <i class="fas fa-hamburger me-1"></i> Produits
            </a>
        </div>
    </div>

    <div class="alert alert-info">
        <h5><i class="fas fa-calendar-day me-2"></i> Statistiques pour le {{ $today->format('d/m/Y') }}</h5>
    </div>

    <!-- Statistiques générales de la journée -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card bg-warning text-dark">
                <div class="card-body">
                    <h5 class="card-title">Commandes en cours</h5>
                    <p class="card-text display-4">{{ $pendingOrders->count() + $preparingOrders->count() + $readyOrders->count() }}</p>
                    <div class="mt-2">
                        <span class="badge bg-light text-dark">En attente: {{ $pendingOrders->count() }}</span>
                        <span class="badge bg-light text-dark">En préparation: {{ $preparingOrders->count() }}</span>
                        <span class="badge bg-light text-dark">Prêtes: {{ $readyOrders->count() }}</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <h5 class="card-title">Commandes validées</h5>
                    <p class="card-text display-4">{{ $completedOrders->count() }}</p>
                    <div class="mt-2">
                        <span class="badge bg-light text-dark">Payées: {{ $completedOrders->count() }}</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <h5 class="card-title">Recettes journalières</h5>
                    <p class="card-text display-4">{{ number_format($dailyRevenue, 0, ',', ' ') }} FCFA</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <!-- Commandes en cours -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0">Commandes en cours</h5>
                </div>
                <div class="card-body">
                    @if($pendingOrders->count() + $preparingOrders->count() + $readyOrders->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>N° Commande</th>
                                        <th>Client</th>
                                        <th>Statut</th>
                                        <th class="text-end">Montant</th>
                                        <th class="text-end">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($pendingOrders as $order)
                                        <tr>
                                            <td>{{ $order->id }}</td>
                                            <td>{{ $order->user->name }}</td>
                                            <td><span class="badge bg-warning text-dark">{{ $order->status_label }}</span></td>
                                            <td class="text-end">{{ number_format($order->total_amount, 0, ',', ' ') }} FCFA</td>
                                            <td class="text-end">
                                                <a href="{{ route('orders.show', $order) }}" class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                    @foreach($preparingOrders as $order)
                                        <tr>
                                            <td>{{ $order->id }}</td>
                                            <td>{{ $order->user->name }}</td>
                                            <td><span class="badge bg-info text-dark">{{ $order->status_label }}</span></td>
                                            <td class="text-end">{{ number_format($order->total_amount, 0, ',', ' ') }} FCFA</td>
                                            <td class="text-end">
                                                <a href="{{ route('orders.show', $order) }}" class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                    @foreach($readyOrders as $order)
                                        <tr>
                                            <td>{{ $order->id }}</td>
                                            <td>{{ $order->user->name }}</td>
                                            <td><span class="badge bg-success">{{ $order->status_label }}</span></td>
                                            <td class="text-end">{{ number_format($order->total_amount, 0, ',', ' ') }} FCFA</td>
                                            <td class="text-end">
                                                <a href="{{ route('orders.show', $order) }}" class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('payments.show', $order) }}" class="btn btn-sm btn-outline-success">
                                                    <i class="fas fa-cash-register"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="alert alert-info">
                            Aucune commande en cours aujourd'hui.
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Commandes validées -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0">Commandes validées</h5>
                </div>
                <div class="card-body">
                    @if($completedOrders->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>N° Commande</th>
                                        <th>Client</th>
                                        <th>Heure</th>
                                        <th class="text-end">Montant</th>
                                        <th class="text-end">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($completedOrders as $order)
                                        <tr>
                                            <td>{{ $order->id }}</td>
                                            <td>{{ $order->user->name }}</td>
                                            <td>{{ $order->updated_at->format('H:i') }}</td>
                                            <td class="text-end">{{ number_format($order->total_amount, 0, ',', ' ') }} FCFA</td>
                                            <td class="text-end">
                                                <a href="{{ route('orders.show', $order) }}" class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('payments.receipt', $order) }}" class="btn btn-sm btn-outline-success">
                                                    <i class="fas fa-receipt"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="alert alert-info">
                            Aucune commande validée aujourd'hui.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Produits vendus aujourd'hui -->
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0">Produits vendus aujourd'hui</h5>
                </div>
                <div class="card-body">
                    @if($soldProducts->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Produit</th>
                                        <th class="text-end">Prix unitaire</th>
                                        <th class="text-end">Quantité vendue</th>
                                        <th class="text-end">Chiffre d'affaires</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($soldProducts as $product)
                                        <tr>
                                            <td>{{ $product->burger->name }}</td>
                                            <td class="text-end">{{ number_format($product->burger->price, 0, ',', ' ') }} FCFA</td>
                                            <td class="text-end">{{ $product->total_quantity }}</td>
                                            <td class="text-end">{{ number_format($product->total_revenue, 0, ',', ' ') }} FCFA</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr class="table-dark">
                                        <th colspan="2">Total</th>
                                        <th class="text-end">{{ $soldProducts->sum('total_quantity') }}</th>
                                        <th class="text-end">{{ number_format($soldProducts->sum('total_revenue'), 0, ',', ' ') }} FCFA</th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    @else
                        <div class="alert alert-info">
                            Aucun produit vendu aujourd'hui.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection 