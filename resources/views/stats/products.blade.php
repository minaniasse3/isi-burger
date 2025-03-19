@extends('layouts.app')

@section('title', 'Statistiques des produits')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Statistiques des produits</h1>
        <div>
            <a href="{{ route('stats.index') }}" class="btn btn-outline-primary me-2">
                <i class="fas fa-chart-bar me-1"></i> Tableau de bord
            </a>
            <a href="{{ route('stats.daily') }}" class="btn btn-outline-primary me-2">
                <i class="fas fa-calendar-day me-1"></i> Journalier
            </a>
            <a href="{{ route('stats.sales') }}" class="btn btn-outline-primary me-2">
                <i class="fas fa-chart-line me-1"></i> Ventes
            </a>
            <a href="{{ route('stats.customers') }}" class="btn btn-outline-primary">
                <i class="fas fa-users me-1"></i> Clients
            </a>
        </div>
    </div>

    <!-- Produits par disponibilité -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0">Produits par disponibilité</h5>
                </div>
                <div class="card-body">
                    <canvas id="availabilityChart" width="400" height="300"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0">Détails des produits par disponibilité</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Disponibilité</th>
                                    <th class="text-end">Nombre de produits</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($productsByAvailability as $availability => $count)
                                    <tr>
                                        <td>{{ $availability }}</td>
                                        <td class="text-end">{{ $count }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr class="table-dark">
                                    <th>Total</th>
                                    <th class="text-end">{{ array_sum($productsByAvailability) }}</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Produits les plus vendus -->
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0">Produits les plus vendus</h5>
                </div>
                <div class="card-body">
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
                                @foreach($topProducts as $product)
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
                                    <th class="text-end">{{ $topProducts->sum('total_quantity') }}</th>
                                    <th class="text-end">{{ number_format($topProducts->sum('total_revenue'), 0, ',', ' ') }} FCFA</th>
                                </tr>
                            </tfoot>
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
    // Graphique des produits par disponibilité
    const availabilityCtx = document.getElementById('availabilityChart').getContext('2d');
    const availabilityChart = new Chart(availabilityCtx, {
        type: 'pie',
        data: {
            labels: {!! json_encode(array_keys($productsByAvailability)) !!},
            datasets: [{
                data: {!! json_encode(array_values($productsByAvailability)) !!},
                backgroundColor: [
                    '#28a745',
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
</script>
@endsection 