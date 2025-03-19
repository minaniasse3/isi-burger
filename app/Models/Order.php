<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'total_amount',
        'status',
        'payment_date',
        'payment_amount',
        'payment_method',
        'notes',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'total_amount' => 'decimal:2',
        'payment_amount' => 'decimal:2',
        'payment_date' => 'datetime',
    ];

    /**
     * Get the user that owns the order.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the items for the order.
     */
    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Get the total number of items in the order.
     */
    public function getTotalItemsAttribute()
    {
        return $this->items->sum('quantity');
    }

    /**
     * Check if the order can be cancelled.
     */
    public function canBeCancelled()
    {
        return in_array($this->status, ['en_attente', 'en_preparation']);
    }

    /**
     * Check if the order is ready.
     */
    public function isReady()
    {
        return $this->status === 'prete';
    }

    /**
     * Check if the order is paid.
     */
    public function isPaid()
    {
        return $this->status === 'payee';
    }

    /**
     * Check if the order is cancelled.
     */
    public function isCancelled()
    {
        return $this->status === 'annulee';
    }

    /**
     * Get the status label.
     */
    public function getStatusLabelAttribute()
    {
        $labels = [
            'en_attente' => 'En attente',
            'en_preparation' => 'En préparation',
            'prete' => 'Prête',
            'payee' => 'Payée',
            'annulee' => 'Annulée',
        ];

        return $labels[$this->status] ?? $this->status;
    }
}
