<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Burger extends Model
{
    use HasFactory;
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'price',
        'image',
        'description',
        'stock',
        'is_available',
        'is_archived',
    ];
    
    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'price' => 'decimal:2',
        'is_available' => 'boolean',
        'is_archived' => 'boolean',
    ];
    
    /**
     * Check if the burger is in stock.
     *
     * @return bool
     */
    public function isInStock(): bool
    {
        return $this->stock > 0 && $this->is_available && !$this->is_archived;
    }
    
    /**
     * Get the order items for the burger.
     */
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }
}
