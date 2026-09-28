<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'user_id',
        'order_id',
        'rating',
        'comment',
        'is_approved',
    ];

    protected function casts(): array
    {
        return [
            'rating' => 'integer',
            'is_approved' => 'boolean',
        ];
    }

    // ==========================================
    // SCOPES & ACCESSORS
    // ==========================================
    public function scopeApproved($query)
    {
        return $query->where('is_approved', true);
    }

    public function getStarHtmlAttribute(): string
    {
        $html = '';
        for ($i = 1; $i <= 5; $i++) {
            if ($i <= $this->rating) {
                $html .= '<i class="bi bi-star-fill text-warning"></i>';
            } else {
                $html .= '<i class="bi bi-star text-muted"></i>';
            }
        }
        return $html;
    }

    public function getStatusBadgeAttribute(): string
    {
        return $this->is_approved
            ? '<span class="badge bg-success">Đã duyệt</span>'
            : '<span class="badge bg-warning text-dark">Chờ duyệt</span>';
    }

    // ==========================================
    // RELATIONSHIPS
    // ==========================================
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
