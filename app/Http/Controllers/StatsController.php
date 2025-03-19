<?php

namespace App\Http\Controllers;

use App\Models\Burger;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class StatsController extends Controller
{
    /**
     * Afficher le tableau de bord des statistiques.
     */
    public function index()
    {
        // Vérifier que l'utilisateur est un gestionnaire
        if (!Auth::user()->isManager()) {
            return redirect()->route('catalog.index')
                ->with('error', 'Vous n\'avez pas les autorisations nécessaires pour accéder à cette page.');
        }

        // Statistiques générales
        $totalOrders = Order::count();
        $totalClients = User::where('role', 'client')->count();
        $totalBurgers = Burger::count();
        $totalRevenue = Order::where('status', 'payee')->sum('total_amount');

        // Commandes par statut
        $ordersByStatus = Order::select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->get()
            ->pluck('count', 'status')
            ->toArray();

        // Burgers les plus populaires
        $popularBurgers = OrderItem::select('burger_id', DB::raw('SUM(quantity) as total_quantity'))
            ->with('burger:id,name')
            ->groupBy('burger_id')
            ->orderByDesc('total_quantity')
            ->limit(5)
            ->get();

        // Chiffre d'affaires par mois (6 derniers mois)
        if (DB::connection()->getDriverName() === 'sqlite') {
            $revenueByMonth = Order::select(
                    DB::raw('strftime("%m", created_at) as month'),
                    DB::raw('strftime("%Y", created_at) as year'),
                    DB::raw('SUM(total_amount) as total_revenue')
                )
                ->where('status', 'payee')
                ->where('created_at', '>=', Carbon::now()->subMonths(6))
                ->groupBy('year', 'month')
                ->orderBy('year')
                ->orderBy('month')
                ->get();
        } else if (DB::connection()->getDriverName() === 'pgsql') {
            $revenueByMonth = Order::select(
                    DB::raw('EXTRACT(MONTH FROM created_at) as month'),
                    DB::raw('EXTRACT(YEAR FROM created_at) as year'),
                    DB::raw('SUM(total_amount) as total_revenue')
                )
                ->where('status', 'payee')
                ->where('created_at', '>=', Carbon::now()->subMonths(6))
                ->groupBy('year', 'month')
                ->orderBy('year')
                ->orderBy('month')
                ->get();
        } else {
            $revenueByMonth = Order::select(
                    DB::raw('MONTH(created_at) as month'),
                    DB::raw('YEAR(created_at) as year'),
                    DB::raw('SUM(total_amount) as total_revenue')
                )
                ->where('status', 'payee')
                ->where('created_at', '>=', Carbon::now()->subMonths(6))
                ->groupBy('year', 'month')
                ->orderBy('year')
                ->orderBy('month')
                ->get();
        }

        $months = [];
        $revenues = [];

        foreach ($revenueByMonth as $revenue) {
            $year = (int)$revenue->year;
            $month = (int)$revenue->month;
            $date = Carbon::createFromDate($year, $month, 1);
            $months[] = $date->format('M Y');
            $revenues[] = $revenue->total_revenue;
        }

        $topClients = Order::select('user_id', DB::raw('COUNT(*) as order_count'), DB::raw('SUM(total_amount) as total_spent'))
            ->with('user:id,name,email')
            ->groupBy('user_id')
            ->orderByDesc('total_spent')
            ->limit(5)
            ->get();

        $today = Carbon::today();
        $pendingOrders = Order::where('status', 'en_attente')
            ->whereDate('created_at', $today)
            ->count();
        $completedOrders = Order::where('status', 'payee')
            ->whereDate('created_at', $today)
            ->count();
        $dailyRevenue = Order::where('status', 'payee')
            ->whereDate('created_at', $today)
            ->sum('total_amount');

        if (DB::connection()->getDriverName() === 'sqlite') {
            $ordersByMonth = Order::select(
                    DB::raw('strftime("%m", created_at) as month'),
                    DB::raw('strftime("%Y", created_at) as year'),
                    DB::raw('COUNT(*) as order_count')
                )
                ->where('created_at', '>=', Carbon::now()->subMonths(6))
                ->groupBy('year', 'month')
                ->orderBy('year')
                ->orderBy('month')
                ->get();
        } else if (DB::connection()->getDriverName() === 'pgsql') {
            $ordersByMonth = Order::select(
                    DB::raw('EXTRACT(MONTH FROM created_at) as month'),
                    DB::raw('EXTRACT(YEAR FROM created_at) as year'),
                    DB::raw('COUNT(*) as order_count')
                )
                ->where('created_at', '>=', Carbon::now()->subMonths(6))
                ->groupBy('year', 'month')
                ->orderBy('year')
                ->orderBy('month')
                ->get();
        } else {
            $ordersByMonth = Order::select(
                    DB::raw('MONTH(created_at) as month'),
                    DB::raw('YEAR(created_at) as year'),
                    DB::raw('COUNT(*) as order_count')
                )
                ->where('created_at', '>=', Carbon::now()->subMonths(6))
                ->groupBy('year', 'month')
                ->orderBy('year')
                ->orderBy('month')
                ->get();
        }

        $orderMonths = [];
        $orderCounts = [];

        foreach ($ordersByMonth as $orderMonth) {
            $year = (int)$orderMonth->year;
            $month = (int)$orderMonth->month;
            $date = Carbon::createFromDate($year, $month, 1);
            $orderMonths[] = $date->format('M Y');
            $orderCounts[] = $orderMonth->order_count;
        }

        if (DB::connection()->getDriverName() === 'sqlite') {
            $productsByCategory = OrderItem::select(
                    DB::raw('strftime("%m", order_items.created_at) as month'),
                    DB::raw('strftime("%Y", order_items.created_at) as year'),
                    DB::raw('CASE WHEN burgers.is_available = 1 THEN "Disponible" ELSE "Indisponible" END as category'),
                    DB::raw('SUM(order_items.quantity) as product_count')
                )
                ->join('burgers', 'order_items.burger_id', '=', 'burgers.id')
                ->where('order_items.created_at', '>=', Carbon::now()->subMonths(6))
                ->groupBy('year', 'month', 'category')
                ->orderBy('year')
                ->orderBy('month')
                ->get();
        } else if (DB::connection()->getDriverName() === 'pgsql') {
            $productsByCategory = OrderItem::select(
                    DB::raw('EXTRACT(MONTH FROM order_items.created_at) as month'),
                    DB::raw('EXTRACT(YEAR FROM order_items.created_at) as year'),
                    DB::raw('CASE WHEN burgers.is_available = true THEN \'Disponible\' ELSE \'Indisponible\' END as category'),
                    DB::raw('SUM(order_items.quantity) as product_count')
                )
                ->join('burgers', 'order_items.burger_id', '=', 'burgers.id')
                ->where('order_items.created_at', '>=', Carbon::now()->subMonths(6))
                ->groupBy('year', 'month', 'category')
                ->orderBy('year')
                ->orderBy('month')
                ->get();
        } else {
            $productsByCategory = OrderItem::select(
                    DB::raw('MONTH(order_items.created_at) as month'),
                    DB::raw('YEAR(order_items.created_at) as year'),
                    DB::raw('CASE WHEN burgers.is_available = 1 THEN "Disponible" ELSE "Indisponible" END as category'),
                    DB::raw('SUM(order_items.quantity) as product_count')
                )
                ->join('burgers', 'order_items.burger_id', '=', 'burgers.id')
                ->where('order_items.created_at', '>=', Carbon::now()->subMonths(6))
                ->groupBy('year', 'month', 'category')
                ->orderBy('year')
                ->orderBy('month')
                ->get();
        }

        $categoryData = [];
        $uniqueCategories = $productsByCategory->pluck('category')->unique();
        $uniqueMonths = [];

        foreach ($productsByCategory as $product) {
            $year = (int)$product->year;
            $month = (int)$product->month;
            $date = Carbon::createFromDate($year, $month, 1);
            $monthLabel = $date->format('M Y');
            
            if (!in_array($monthLabel, $uniqueMonths)) {
                $uniqueMonths[] = $monthLabel;
            }
            
            if (!isset($categoryData[$product->category])) {
                $categoryData[$product->category] = [];
            }
            
            $categoryData[$product->category][$monthLabel] = $product->product_count;
        }

        // Formater les données pour Chart.js
        $categoryDatasets = [];
        $colors = ['#007bff', '#28a745', '#ffc107', '#dc3545', '#17a2b8'];
        $colorIndex = 0;

        foreach ($uniqueCategories as $category) {
            $data = [];
            foreach ($uniqueMonths as $month) {
                $data[] = $categoryData[$category][$month] ?? 0;
            }
            
            $categoryDatasets[] = [
                'label' => $category,
                'data' => $data,
                'backgroundColor' => $colors[$colorIndex % count($colors)],
                'borderColor' => $colors[$colorIndex % count($colors)],
                'borderWidth' => 1
            ];
            
            $colorIndex++;
        }

        return view('stats.index', compact(
            'totalOrders',
            'totalClients',
            'totalBurgers',
            'totalRevenue',
            'ordersByStatus',
            'popularBurgers',
            'months',
            'revenues',
            'topClients',
            'pendingOrders',
            'completedOrders',
            'dailyRevenue',
            'orderMonths',
            'orderCounts',
            'uniqueMonths',
            'categoryDatasets'
        ));
    }

   
    public function sales()
    {
        // Vérifier que l'utilisateur est un gestionnaire
        if (!Auth::user()->isManager()) {
            return redirect()->route('catalog.index')
                ->with('error', 'Vous n\'avez pas les autorisations nécessaires pour accéder à cette page.');
        }

        // Ventes par jour (30 derniers jours)
        if (DB::connection()->getDriverName() === 'sqlite') {
            $salesByDay = Order::select(
                    DB::raw('date(created_at) as date'),
                    DB::raw('COUNT(*) as order_count'),
                    DB::raw('SUM(total_amount) as total_sales')
                )
                ->where('status', 'payee')
                ->where('created_at', '>=', Carbon::now()->subDays(30))
                ->groupBy('date')
                ->orderBy('date')
                ->get();
        } else if (DB::connection()->getDriverName() === 'pgsql') {
            $salesByDay = Order::select(
                    DB::raw('DATE(created_at) as date'),
                    DB::raw('COUNT(*) as order_count'),
                    DB::raw('SUM(total_amount) as total_sales')
                )
                ->where('status', 'payee')
                ->where('created_at', '>=', Carbon::now()->subDays(30))
                ->groupBy('date')
                ->orderBy('date')
                ->get();
        } else {
            // MySQL ou autres
            $salesByDay = Order::select(
                    DB::raw('DATE(created_at) as date'),
                    DB::raw('COUNT(*) as order_count'),
                    DB::raw('SUM(total_amount) as total_sales')
                )
                ->where('status', 'payee')
                ->where('created_at', '>=', Carbon::now()->subDays(30))
                ->groupBy('date')
                ->orderBy('date')
                ->get();
        }

        // Ventes par méthode de paiement
        $salesByPaymentMethod = Order::select(
                'payment_method',
                DB::raw('COUNT(*) as order_count'),
                DB::raw('SUM(total_amount) as total_sales')
            )
            ->where('status', 'payee')
            ->groupBy('payment_method')
            ->get();

        return view('stats.sales', compact('salesByDay', 'salesByPaymentMethod'));
    }

    /**
     * Afficher les statistiques des produits.
     */
    public function products()
    {
        // Vérifier que l'utilisateur est un gestionnaire
        if (!Auth::user()->isManager()) {
            return redirect()->route('catalog.index')
                ->with('error', 'Vous n\'avez pas les autorisations nécessaires pour accéder à cette page.');
        }

        // Produits les plus vendus
        $topProducts = OrderItem::select(
                'burger_id',
                DB::raw('SUM(quantity) as total_quantity'),
                DB::raw('SUM(subtotal) as total_revenue')
            )
            ->with('burger')
            ->groupBy('burger_id')
            ->orderByDesc('total_quantity')
            ->get();

        // Produits par disponibilité
        if (DB::connection()->getDriverName() === 'pgsql') {
            $productsByAvailability = Burger::select(
                    DB::raw('CASE WHEN is_available = true AND stock > 0 THEN \'Disponible\' ELSE \'Indisponible\' END as availability'),
                    DB::raw('COUNT(*) as count')
                )
                ->groupBy('availability')
                ->get()
                ->pluck('count', 'availability')
                ->toArray();
        } else {
            $productsByAvailability = Burger::select(
                    DB::raw('CASE WHEN is_available = 1 AND stock > 0 THEN "Disponible" ELSE "Indisponible" END as availability'),
                    DB::raw('COUNT(*) as count')
                )
                ->groupBy('availability')
                ->get()
                ->pluck('count', 'availability')
                ->toArray();
        }

        return view('stats.products', compact('topProducts', 'productsByAvailability'));
    }

    /**
     * Afficher les statistiques des clients.
     */
    public function customers()
    {
        // Vérifier que l'utilisateur est un gestionnaire
        if (!Auth::user()->isManager()) {
            return redirect()->route('catalog.index')
                ->with('error', 'Vous n\'avez pas les autorisations nécessaires pour accéder à cette page.');
        }

        // Clients les plus actifs
        $topCustomers = Order::select(
                'user_id',
                DB::raw('COUNT(*) as order_count'),
                DB::raw('SUM(total_amount) as total_spent')
            )
            ->with('user')
            ->groupBy('user_id')
            ->orderByDesc('total_spent')
            ->limit(20)
            ->get();

        // Nouveaux clients par mois (12 derniers mois)
        if (DB::connection()->getDriverName() === 'sqlite') {
            $newCustomersByMonth = User::select(
                    DB::raw('strftime("%m", created_at) as month'),
                    DB::raw('strftime("%Y", created_at) as year'),
                    DB::raw('COUNT(*) as count')
                )
                ->where('role', 'client')
                ->where('created_at', '>=', Carbon::now()->subMonths(12))
                ->groupBy('year', 'month')
                ->orderBy('year')
                ->orderBy('month')
                ->get();
        } else if (DB::connection()->getDriverName() === 'pgsql') {
            $newCustomersByMonth = User::select(
                    DB::raw('EXTRACT(MONTH FROM created_at) as month'),
                    DB::raw('EXTRACT(YEAR FROM created_at) as year'),
                    DB::raw('COUNT(*) as count')
                )
                ->where('role', 'client')
                ->where('created_at', '>=', Carbon::now()->subMonths(12))
                ->groupBy('year', 'month')
                ->orderBy('year')
                ->orderBy('month')
                ->get();
        } else {
            // MySQL ou autres
            $newCustomersByMonth = User::select(
                    DB::raw('MONTH(created_at) as month'),
                    DB::raw('YEAR(created_at) as year'),
                    DB::raw('COUNT(*) as count')
                )
                ->where('role', 'client')
                ->where('created_at', '>=', Carbon::now()->subMonths(12))
                ->groupBy('year', 'month')
                ->orderBy('year')
                ->orderBy('month')
                ->get();
        }

        return view('stats.customers', compact('topCustomers', 'newCustomersByMonth'));
    }

    /**
     * Afficher les statistiques journalières.
     */
    public function daily()
    {
        // Vérifier que l'utilisateur est un gestionnaire
        if (!Auth::user()->isManager()) {
            return redirect()->route('catalog.index')
                ->with('error', 'Vous n\'avez pas les autorisations nécessaires pour accéder à cette page.');
        }

        $today = Carbon::today();
        
        // Commandes en cours de la journée
        $pendingOrders = Order::where('status', 'en_attente')
            ->whereDate('created_at', $today)
            ->with('user:id,name,email')
            ->get();
            
        // Commandes en préparation de la journée
        $preparingOrders = Order::where('status', 'en_preparation')
            ->whereDate('created_at', $today)
            ->with('user:id,name,email')
            ->get();
            
        // Commandes prêtes de la journée
        $readyOrders = Order::where('status', 'prete')
            ->whereDate('created_at', $today)
            ->with('user:id,name,email')
            ->get();
            
        // Commandes validées (payées) de la journée
        $completedOrders = Order::where('status', 'payee')
            ->whereDate('created_at', $today)
            ->with('user:id,name,email')
            ->get();
            
        // Recettes journalières
        $dailyRevenue = Order::where('status', 'payee')
            ->whereDate('created_at', $today)
            ->sum('total_amount');
            
        // Commandes annulées de la journée
        $cancelledOrders = Order::where('status', 'annulee')
            ->whereDate('created_at', $today)
            ->with('user:id,name,email')
            ->get();
            
        // Produits vendus aujourd'hui
        $soldProducts = OrderItem::select(
                'burger_id',
                DB::raw('SUM(quantity) as total_quantity'),
                DB::raw('SUM(subtotal) as total_revenue')
            )
            ->with('burger:id,name,price')
            ->whereHas('order', function ($query) use ($today) {
                $query->whereDate('created_at', $today)
                    ->where('status', 'payee');
            })
            ->groupBy('burger_id')
            ->orderByDesc('total_quantity')
            ->get();

        return view('stats.daily', compact(
            'pendingOrders',
            'preparingOrders',
            'readyOrders',
            'completedOrders',
            'cancelledOrders',
            'dailyRevenue',
            'soldProducts',
            'today'
        ));
    }
}
