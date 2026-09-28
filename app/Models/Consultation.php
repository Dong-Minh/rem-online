<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Consultation extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'user_id',
        'customer_name',
        'phone',
        'email',
        'address',
        'province',
        'district',
        'ward',
        'preferred_date',
        'preferred_time_slot',
        'curtain_types',
        'estimated_windows',
        'notes',
        'status',
        'assigned_staff_id',
        'admin_notes',
        'quoted_amount',
    ];

    protected $casts = [
        'preferred_date' => 'date',
        'curtain_types' => 'array',
        'quoted_amount' => 'decimal:2',
    ];

    /**
     * Khách hàng đăng ký (nếu có tài khoản)
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Thợ kỹ thuật / Nhân viên phụ trách đo đạc
     */
    public function assignedStaff()
    {
        return $this->belongsTo(User::class, 'assigned_staff_id');
    }

    /**
     * Format khung giờ hẹn sang Tiếng Việt
     */
    public function getTimeSlotTextAttribute(): string
    {
        return match ($this->preferred_time_slot) {
            'morning' => 'Sáng (08:00 - 12:00)',
            'afternoon' => 'Chiều (13:30 - 17:30)',
            'evening' => 'Tối (18:00 - 20:30)',
            default => 'Trong giờ hành chính',
        };
    }

    /**
     * Danh sách loại rèm quan tâm dạng chuỗi
     */
    public function getCurtainTypesTextAttribute(): string
    {
        if (is_array($this->curtain_types) && count($this->curtain_types) > 0) {
            return implode(', ', $this->curtain_types);
        }
        return 'Tất cả mẫu vải cao cấp';
    }

    /**
     * Địa chỉ khảo sát đầy đủ
     */
    public function getFullAddressAttribute(): string
    {
        $parts = array_filter([
            $this->address,
            $this->ward,
            $this->district,
            $this->province,
        ]);
        return implode(', ', $parts);
    }

    /**
     * Badge trạng thái HTML
     */
    public function getStatusBadgeAttribute(): string
    {
        $config = match ($this->status) {
            'pending' => ['bg-warning text-dark', 'Chờ tiếp nhận', 'bi-hourglass-split'],
            'assigned' => ['bg-info text-dark', 'Đã phân công thợ', 'bi-person-check'],
            'measuring' => ['bg-primary text-white', 'Đang đo đạc tại nhà', 'bi-rulers'],
            'quoted' => ['bg-secondary text-white', 'Đã báo giá', 'bi-receipt'],
            'completed' => ['bg-success text-white', 'Đã chốt may đo', 'bi-check-circle-fill'],
            'cancelled' => ['bg-danger text-white', 'Đã hủy lịch', 'bi-x-circle-fill'],
            default => ['bg-light text-dark border', $this->status, 'bi-info-circle'],
        };

        return sprintf(
            '<span class="badge %s px-2 py-1 rounded-pill small fw-semibold"><i class="bi %s me-1"></i>%s</span>',
            $config[0],
            $config[2],
            $config[1]
        );
    }

    /**
     * Sinh mã lịch hẹn tự động (CS-2026-XXXX)
     */
    public static function generateCode(): string
    {
        $year = date('Y');
        $random = strtoupper(substr(md5(uniqid(mt_rand(), true)), 0, 5));
        return "CS-{$year}-{$random}";
    }
}
