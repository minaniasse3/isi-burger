@extends('layouts.app')

@section('title', 'Statistiques des ventes')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Statistiques des ventes</h1>
        <div>
            <a href="{{ route('stats.index') }}" class="btn btn-outline-primary me-2">
                <i class="fas fa-chart-bar me-1"></i> Tableau de bord
            </a>
            <a href="{{ route('stats.daily') }}" class="btn btn-outline-primary me-2">
                <i class="fas fa-calendar-day me-1"></i> Journalier
            </a>
            <a href="{{ route('stats.products') }}" class="btn btn-outline-primary me-2">
                <i class="fas fa-hamburger me-1"></i> Produits
            </a>
            <a href="{{ route('stats.customers') }}" class="btn btn-outline-primary">
                <i class="fas fa-users me-1"></i> Clients
            </a>
        </div>
    </div>

    <!-- Ventes par jour (30 derniers jours) -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0">Ventes par jour (30 derniers jours)</h5>
                </div>
                <div class="card-body">
                    <canvas id="salesByDayChart" width="400" height="200"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Ventes par méthode de paiement -->
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0">Ventes par méthode de paiement</h5>
                </div>
                <div class="card-body">
                    <canvas id="paymentMethodChart" width="400" height="300"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0">Détails des ventes par méthode de paiement</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Méthode de paiement</th>
                                    <th class="text-end">Nombre de commandes</th>
                                    <th class="text-end">Montant total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($salesByPaymentMethod as $sale)
                                    <tr>
                                        <td>{{ $sale->payment_method === 'especes' ? 'Espèces' : 'Carte bancaire' }}</td>
                                        <td class="text-end">{{ $sale->order_count }}</td>
                                        <td class="text-end">{{ number_format($sale->total_sales, 0, ',', ' ') }} FCFA</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr class="table-dark">
                                    <th>Total</th>
                                    <th class="text-end">{{ $salesByPaymentMethod->sum('order_count') }}</th>
                                    <th class="text-end">{{ number_format($salesByPaymentMethod->sum('total_sales'), 0, ',', ' ') }} FCFA</th>
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
    // Graphique des ventes par jour
    const salesByDayCtx = document.getElementById('salesByDayChart').getContext('2d');
    const salesByDayChart = new Chart(salesByDayCtx, {
        type: 'line',
        data: {
            labels: {!! json_encode($salesByDay->pluck('date')) !!},
            datasets: [{
                label: 'Montant des ventes (FCFA)',
                data: {!! json_encode($salesByDay->pluck('total_sales')) !!},
                backgroundColor: 'rgba(0, 123, 255, 0.2)',
                borderColor: '#007bff',
                borderWidth: 2,
                tension: 0.1
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

    // Graphique des ventes par méthode de paiement
    const paymentMethodCtx = document.getElementById('paymentMethodChart').getContext('2d');
    const paymentMethodChart = new Chart(paymentMethodCtx, {
        type: 'pie',
        data: {
            labels: {!! json_encode($salesByPaymentMethod->map(function($item) {
                return $item->payment_method === 'especes' ? 'Espèces' : 'Carte bancaire';
            })) !!},
            datasets: [{
                data: {!! json_encode($salesByPaymentMethod->pluck('total_sales')) !!},
                backgroundColor: [
                    '#28a745',
                    '#007bff'
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