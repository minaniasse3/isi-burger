@extends('layouts.app')

@section('title', 'Statistiques des clients')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Statistiques des clients</h1>
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
            <a href="{{ route('stats.products') }}" class="btn btn-outline-primary">
                <i class="fas fa-hamburger me-1"></i> Produits
            </a>
        </div>
    </div>

    <!-- Nouveaux clients par mois -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0">Nouveaux clients par mois</h5>
                </div>
                <div class="card-body">
                    <canvas id="newCustomersChart" width="400" height="200"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Clients les plus actifs -->
    <div class="row">
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
                                    <th class="text-end">Montant total dépensé</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($topCustomers as $customer)
                                    <tr>
                                        <td>{{ $customer->user->name }}</td>
                                        <td>{{ $customer->user->email }}</td>
                                        <td class="text-end">{{ $customer->order_count }}</td>
                                        <td class="text-end">{{ number_format($customer->total_spent, 0, ',', ' ') }} FCFA</td>
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
    // Préparer les données pour le graphique
    const months = [];
    const counts = [];
    
    @foreach($newCustomersByMonth as $data)
        @php
            $year = (int)$data->year;
            $month = (int)$data->month;
            $date = \Carbon\Carbon::createFromDate($year, $month, 1);
        @endphp
        months.push('{{ $date->format('M Y') }}');
        counts.push({{ $data->count }});
    @endforeach
    
    // Graphique des nouveaux clients par mois
    const newCustomersCtx = document.getElementById('newCustomersChart').getContext('2d');
    const newCustomersChart = new Chart(newCustomersCtx, {
        type: 'bar',
        data: {
            labels: months,
            datasets: [{
                label: 'Nouveaux clients',
                data: counts,
                backgroundColor: '#17a2b8',
                borderColor: '#138496',
                borderWidth: 1
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
</script>
@endsection 