<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_code',
        'user_id',
        'address_id',
        'recipient_name',
        'recipient_phone',
        'recipient_email',
        'shipping_address',
        'subtotal',
        'promotion_discount',
        'voucher_discount',
        'voucher_id',
        'shipping_fee',
        'total',
        'payment_method',
        'payment_status',
        'status',
        'shipping_status',
        'ghn_order_code',
        'ghn_total_fee',
        'to_province_id',
        'to_district_id',
        'to_ward_code',
        'note',
    ];

    protected function casts(): array
    {
        return [
            'subtotal' => 'decimal:2',
            'promotion_discount' => 'decimal:2',
            'voucher_discount' => 'decimal:2',
            'shipping_fee' => 'decimal:2',
            'total' => 'decimal:2',
            'ghn_total_fee' => 'integer',
            'to_district_id' => 'integer',
            'to_province_id' => 'integer',
        ];
    }

    // ==========================================
    // COMPATIBILITY ACCESSORS (Chuẩn hóa LAB)
    // ==========================================
    public function getNameAttribute(): string
    {
        return $this->recipient_name;
    }

    public function getPhoneAttribute(): string
    {
        return $this->recipient_phone;
    }

    public function getAddressAttribute(): string
    {
        return $this->shipping_address;
    }

    public function getTotalPriceAttribute(): float
    {
        return (float) $this->total;
    }

    // ==========================================
    // FORMATTED HELPERS
    // ==========================================
    public function getFormattedTotalAttribute(): string
    {
        return number_format($this->total, 0, ',', '.') . ' đ';
    }

    public function getFormattedSubtotalAttribute(): string
    {
        return number_format($this->subtotal, 0, ',', '.') . ' đ';
    }

    public function getFormattedShippingFeeAttribute(): string
    {
        return number_format($this->shipping_fee, 0, ',', '.') . ' đ';
    }

    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {
            'pending' => '<span class="badge bg-warning text-dark">Chờ xác nhận</span>',
            'confirmed' => '<span class="badge bg-info text-dark">Đã xác nhận</span>',
            'preparing' => '<span class="badge bg-primary">Đang may đo</span>',
            'shipping' => '<span class="badge bg-indigo text-white" style="background-color: #6366f1;">Đang giao hàng</span>',
            'delivered' => '<span class="badge bg-success">Đã giao thành công</span>',
            'completed' => '<span class="badge bg-success">Hoàn tất</span>',
            'cancelled' => '<span class="badge bg-danger">Đã hủy</span>',
            default => '<span class="badge bg-secondary">' . $this->status . '</span>',
        };
    }

    public function getShippingStatusBadgeAttribute(): string
    {
        return match ($this->shipping_status) {
            'not_shipped' => '<span class="badge bg-secondary">Chưa bàn giao GHN</span>',
            'pending' => '<span class="badge bg-warning text-dark">Chờ GHN lấy hàng</span>',
            'ready_to_pick' => '<span class="badge bg-info text-dark">GHN chuẩn bị lấy</span>',
            'delivering' => '<span class="badge bg-primary">GHN đang giao</span>',
            'delivered' => '<span class="badge bg-success">GHN đã giao</span>',
            'cancelled' => '<span class="badge bg-danger">Đã hủy vận đơn</span>',
            default => '<span class="badge bg-secondary">' . $this->shipping_status . '</span>',
        };
    }

    public function getPaymentStatusBadgeAttribute(): string
    {
        return match ($this->payment_status) {
            'pending' => '<span class="badge bg-warning text-dark">Chưa thanh toán</span>',
            'paid' => '<span class="badge bg-success">Đã thanh toán</span>',
            'failed' => '<span class="badge bg-danger">Thanh toán lỗi</span>',
            'refunded' => '<span class="badge bg-secondary">Đã hoàn tiền</span>',
            default => '<span class="badge bg-secondary">' . $this->payment_status . '</span>',
        };
    }

    public function getPaymentMethodNameAttribute(): string
    {
        return match ($this->payment_method) {
            'cod' => 'Thanh toán khi nhận (COD)',
            'momo' => 'Ví điện tử MoMo (ATM / QR)',
            'bank_transfer' => 'Chuyển khoản ngân hàng (VietQR)',
            default => $this->payment_method ?? 'COD',
        };
    }

    // ==========================================
    // RELATIONSHIPS
    // ==========================================
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function voucher()
    {
        return $this->belongsTo(Voucher::class);
    }

    public function address()
    {
        return $this->belongsTo(Address::class);
    }

    public function paymentTransactions()
    {
        return $this->hasMany(PaymentTransaction::class);
    }
}
