<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BurgerController;
use App\Http\Controllers\CatalogController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\StatsController;
use App\Http\Controllers\DashboardController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return redirect()->route('catalog.index');
});

// Route pour le tableau de bord
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

// Routes pour la gestion des burgers (admin)
Route::middleware(['auth', 'manager'])->group(function () {
    Route::resource('burgers', BurgerController::class);
    Route::get('/burgers/archived', [BurgerController::class, 'archived'])->name('burgers.archived');
    Route::patch('/burgers/{burger}/archive', [BurgerController::class, 'archive'])->name('burgers.archive');
    Route::patch('/burgers/{burger}/restore', [BurgerController::class, 'restore'])->name('burgers.restore');
    Route::patch('/burgers/{burger}/stock', [BurgerController::class, 'updateStock'])->name('burgers.updateStock');
});

// Routes pour le catalogue (public)
Route::get('/catalog', [CatalogController::class, 'index'])->name('catalog.index');
Route::get('/catalog/{burger}', [CatalogController::class, 'show'])->name('catalog.show');

// Routes pour le panier
Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::post('/cart/add/{burger}', [CartController::class, 'add'])->name('cart.add');
Route::patch('/cart/update/{id}', [CartController::class, 'update'])->name('cart.update');
Route::delete('/cart/remove/{id}', [CartController::class, 'remove'])->name('cart.remove');
Route::delete('/cart/clear', [CartController::class, 'clear'])->name('cart.clear');
Route::get('/cart/checkout', [CartController::class, 'checkout'])->middleware('auth')->name('cart.checkout');

// Routes pour les commandes
Route::middleware('auth')->group(function () {
    Route::post('/orders', [OrderController::class, 'store'])->name('orders.store');
    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
    Route::patch('/orders/{order}/cancel', [OrderController::class, 'cancel'])->name('orders.cancel');
    
    // Routes pour les gestionnaires uniquement
    Route::middleware('manager')->group(function () {
        Route::patch('/orders/{order}/status', [OrderController::class, 'updateStatus'])->name('orders.updateStatus');
    });
});

// Routes pour les paiements (gestionnaires uniquement)
Route::middleware(['auth', 'manager'])->group(function () {
    Route::get('/payments', [PaymentController::class, 'index'])->name('payments.index');
    Route::get('/payments/{order}', [PaymentController::class, 'show'])->name('payments.show');
    Route::post('/payments/{order}/process', [PaymentController::class, 'process'])->name('payments.process');
});

// Routes pour les statistiques (gestionnaires uniquement)
Route::middleware(['auth', 'manager'])->group(function () {
    Route::get('/stats', [StatsController::class, 'index'])->name('stats.index');
    Route::get('/stats/sales', [StatsController::class, 'sales'])->name('stats.sales');
    Route::get('/stats/products', [StatsController::class, 'products'])->name('stats.products');
    Route::get('/stats/customers', [StatsController::class, 'customers'])->name('stats.customers');
    Route::get('/stats/daily', [StatsController::class, 'daily'])->name('stats.daily');
});

// Route pour le reçu de paiement (accessible par le gestionnaire et le client)
Route::middleware('auth')->get('/payments/{order}/receipt', [PaymentController::class, 'receipt'])->name('payments.receipt');

// Routes pour l'authentification
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/profile', [AuthController::class, 'profile'])->name('profile');
    Route::patch('/profile', [AuthController::class, 'updateProfile'])->name('profile.update');
});
