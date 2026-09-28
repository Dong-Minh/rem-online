<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CartItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'cart_id',
        'product_id',
        'color_id',
        'width',
        'height',
        'area',
        'unit_price',
        'sale_unit_price',
        'quantity',
    ];

    protected function casts(): array
    {
        return [
            'width' => 'decimal:2',
            'height' => 'decimal:2',
            'area' => 'decimal:4',
            'unit_price' => 'decimal:2',
            'sale_unit_price' => 'decimal:2',
            'quantity' => 'integer',
        ];
    }

    public function cart()
    {
        return $this->belongsTo(Cart::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function color()
    {
        return $this->belongsTo(Color::class);
    }

    /**
     * Thành tiền của item trong giỏ = area × price × quantity
     */
    public function getItemTotalAttribute(): float
    {
        $price = $this->sale_unit_price ?? $this->unit_price;
        return (float) ($this->area * $price * $this->quantity);
    }
}
