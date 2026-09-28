<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Voucher extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'description',
        'discount_type',
        'discount_value',
        'max_discount_amount',
        'min_order_amount',
        'usage_limit',
        'usage_per_user',
        'starts_at',
        'ends_at',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'discount_value' => 'decimal:2',
            'max_discount_amount' => 'decimal:2',
            'min_order_amount' => 'decimal:2',
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
            'is_active' => 'boolean',
        ];
    }

    public function usages()
    {
        return $this->hasMany(VoucherUsage::class);
    }

    // ==========================================
    // SCOPES & HELPERS
    // ==========================================
    public function scopeActive($query)
    {
        return $query->where('is_active', true)
            ->where('starts_at', '<=', now())
            ->where('ends_at', '>=', now());
    }

    public function isValidNow(): bool
    {
        return $this->is_active && now()->between($this->starts_at, $this->ends_at);
    }

    public function hasReachedLimit(): bool
    {
        if ($this->usage_limit === null) {
            return false;
        }
        return $this->usages()->count() >= $this->usage_limit;
    }

    public function hasUserReachedLimit(User $user): bool
    {
        if ($this->usage_per_user <= 0) {
            return false;
        }
        return $this->usages()->where('user_id', $user->id)->count() >= $this->usage_per_user;
    }

    public function getDiscountLabelAttribute(): string
    {
        if ($this->discount_type === 'percent') {
            $label = 'Giảm ' . (int) $this->discount_value . '%';
            if ($this->max_discount_amount) {
                $label .= ' (Tối đa ' . number_format($this->max_discount_amount, 0, ',', '.') . 'đ)';
            }
            return $label;
        }
        return 'Giảm ' . number_format($this->discount_value, 0, ',', '.') . ' đ';
    }

    public function getStatusBadgeAttribute(): string
    {
        if (!$this->is_active) {
            return '<span class="badge bg-secondary">Tạm tắt</span>';
        }
        if (now()->lt($this->starts_at)) {
            return '<span class="badge bg-info text-dark">Chưa bắt đầu</span>';
        }
        if (now()->gt($this->ends_at)) {
            return '<span class="badge bg-danger">Hết hạn</span>';
        }
        if ($this->hasReachedLimit()) {
            return '<span class="badge bg-warning text-dark">Hết lượt dùng</span>';
        }
        return '<span class="badge bg-success">Đang hiệu lực</span>';
    }
}
