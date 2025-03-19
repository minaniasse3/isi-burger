@extends('layouts.app')

@section('title', 'Tableau de bord - Statistiques')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Tableau de bord</h1>
        <div>
            <a href="{{ route('stats.daily') }}" class="btn btn-outline-primary me-2">
                <i class="fas fa-calendar-day me-1"></i> Journalier
            </a>
            <a href="{{ route('stats.sales') }}" class="btn btn-outline-primary me-2">
                <i class="fas fa-chart-line me-1"></i> Ventes
            </a>
            <a href="{{ route('stats.products') }}" class="btn btn-outline-primary me-2">
                <i class="fas fa-hamburger me-1"></i> Produits
            </a>
            <a href="{{ route('stats.customers') }}" class="btn btn-outline-primary">
                <i class="fas fa-users me-1"></i> Clients
            </a>
        </div>
    </div>

    <!-- Statistiques journalières -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card bg-warning text-dark">
                <div class="card-body">
                    <h5 class="card-title">Commandes en cours aujourd'hui</h5>
                    <p class="card-text display-4">{{ $pendingOrders }}</p>
                    <a href="{{ route('stats.daily') }}" class="btn btn-sm btn-dark mt-2">Voir détails</a>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <h5 class="card-title">Commandes validées aujourd'hui</h5>
                    <p class="card-text display-4">{{ $completedOrders }}</p>
                    <a href="{{ route('stats.daily') }}" class="btn btn-sm btn-dark mt-2">Voir détails</a>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <h5 class="card-title">Recettes journalières</h5>
                    <p class="card-text display-4">{{ number_format($dailyRevenue, 0, ',', ' ') }} FCFA</p>
                    <a href="{{ route('stats.daily') }}" class="btn btn-sm btn-dark mt-2">Voir détails</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistiques générales -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <h5 class="card-title">Commandes</h5>
                    <p class="card-text display-4">{{ $totalOrders }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <h5 class="card-title">Chiffre d'affaires</h5>
                    <p class="card-text display-4">{{ number_format($totalRevenue, 0, ',', ' ') }} FCFA</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <h5 class="card-title">Clients</h5>
                    <p class="card-text display-4">{{ $totalClients }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-dark">
                <div class="card-body">
                    <h5 class="card-title">Produits</h5>
                    <p class="card-text display-4">{{ $totalBurgers }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <!-- Commandes par statut -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0">Commandes par statut</h5>
                </div>
                <div class="card-body">
                    <canvas id="orderStatusChart" width="400" height="300"></canvas>
                </div>
            </div>
        </div>

        <!-- Burgers les plus populaires -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0">Burgers les plus populaires</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Burger</th>
                                    <th class="text-end">Quantité vendue</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($popularBurgers as $burger)
                                    <tr>
                                        <td>{{ $burger->burger->name }}</td>
                                        <td class="text-end">{{ $burger->total_quantity }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <!-- Nombre de commandes par mois -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0">Nombre de commandes par mois</h5>
                </div>
                <div class="card-body">
                    <canvas id="ordersByMonthChart" width="400" height="300"></canvas>
                </div>
            </div>
        </div>

        <!-- Nombre de produits par catégorie par mois -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0">Produits par catégorie par mois</h5>
                </div>
                <div class="card-body">
                    <canvas id="productsByCategoryChart" width="400" height="300"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <!-- Chiffre d'affaires par mois -->
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0">Chiffre d'affaires par mois</h5>
                </div>
                <div class="card-body">
                    <canvas id="revenueChart" width="400" height="200"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Clients les plus actifs -->
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0">Clients les plus actifs</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Client</th>
                                    <th>Email</th>
                                    <th class="text-end">Nombre de commandes</th>
                                    <th class="text-end">Montant total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($topClients as $client)
                                    <tr>
                                        <td>{{ $client->user->name }}</td>
                                        <td>{{ $client->user->email }}</td>
                                        <td class="text-end">{{ $client->order_count }}</td>
                                        <td class="text-end">{{ number_format($client->total_spent, 0, ',', ' ') }} FCFA</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Graphique des commandes par statut
    const orderStatusCtx = document.getElementById('orderStatusChart').getContext('2d');
    const orderStatusChart = new Chart(orderStatusCtx, {
        type: 'pie',
        data: {
            labels: [
                'En attente', 
                'En préparation', 
                'Prête', 
                'Payée', 
                'Annulée'
            ],
            datasets: [{
                data: [
                    {{ $ordersByStatus['en_attente'] ?? 0 }},
                    {{ $ordersByStatus['en_preparation'] ?? 0 }},
                    {{ $ordersByStatus['prete'] ?? 0 }},
                    {{ $ordersByStatus['payee'] ?? 0 }},
                    {{ $ordersByStatus['annulee'] ?? 0 }}
                ],
                backgroundColor: [
                    '#ffc107',
                    '#17a2b8',
                    '#28a745',
                    '#007bff',
                    '#dc3545'
                ]
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'right',
                }
            }
        }
    });

    // Graphique du chiffre d'affaires par mois
    const revenueCtx = document.getElementById('revenueChart').getContext('2d');
    const revenueChart = new Chart(revenueCtx, {
        type: 'bar',
        data: {
            labels: {!! json_encode($months) !!},
            datasets: [{
                label: 'Chiffre d\'affaires (FCFA)',
                data: {!! json_encode($revenues) !!},
                backgroundColor: '#007bff',
                borderColor: '#0056b3',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });

    // Graphique du nombre de commandes par mois
    const ordersByMonthCtx = document.getElementById('ordersByMonthChart').getContext('2d');
    const ordersByMonthChart = new Chart(ordersByMonthCtx, {
        type: 'line',
        data: {
            labels: {!! json_encode($orderMonths) !!},
            datasets: [{
                label: 'Nombre de commandes',
                data: {!! json_encode($orderCounts) !!},
                backgroundColor: 'rgba(40, 167, 69, 0.2)',
                borderColor: '#28a745',
                borderWidth: 2,
                tension: 0.1
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        precision: 0
                    }
                }
            }
        }
    });

    // Graphique des produits par catégorie par mois
    const productsByCategoryCtx = document.getElementById('productsByCategoryChart').getContext('2d');
    const productsByCategoryChart = new Chart(productsByCategoryCtx, {
        type: 'bar',
        data: {
            labels: {!! json_encode($uniqueMonths) !!},
            datasets: {!! json_encode($categoryDatasets) !!}
        },
        options: {
            responsive: true,
            scales: {
                x: {
                    stacked: true,
                },
                y: {
                    stacked: true,
                    beginAtZero: true,
                    ticks: {
                        precision: 0
                    }
                }
            }
        }
    });
</script>
@endsection 