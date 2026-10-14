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
        'name',
        'phone',
        'email',
        'address',
        'province',
        'district',
        'ward',
        'preferred_date',
        'preferred_time_slot',
        'curtain_types',
        'curtain_type_interest',
        'estimated_windows',
        'estimated_width',
        'estimated_height',
        'preferred_contact_time',
        'notes',
        'content',
        'status',
        'assigned_staff_id',
        'admin_notes',
        'admin_note',
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
     * Accessor: Tên khách hàng (hỗ trợ cả customer_name và name)
     */
    public function getCustomerNameAttribute(): string
    {
        return $this->attributes['customer_name'] ?? $this->attributes['name'] ?? 'Khách hàng';
    }

    /**
     * Accessor: name (hỗ trợ legacy)
     */
    public function getNameAttribute(): string
    {
        return $this->attributes['name'] ?? $this->attributes['customer_name'] ?? 'Khách hàng';
    }

    /**
     * Accessor: Ghi chú của khách (hỗ trợ cả notes và content)
     */
    public function getNotesAttribute(): ?string
    {
        return $this->attributes['notes'] ?? $this->attributes['content'] ?? null;
    }

    /**
     * Accessor: content (hỗ trợ legacy)
     */
    public function getContentAttribute(): ?string
    {
        return $this->attributes['content'] ?? $this->attributes['notes'] ?? null;
    }

    /**
     * Accessor: Ghi chú quản trị (hỗ trợ cả admin_notes và admin_note)
     */
    public function getAdminNotesAttribute(): ?string
    {
        return $this->attributes['admin_notes'] ?? $this->attributes['admin_note'] ?? null;
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
        if (!empty($this->curtain_type_interest)) {
            return $this->curtain_type_interest;
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
        return count($parts) > 0 ? implode(', ', $parts) : 'Chưa cập nhật địa chỉ';
    }

    /**
     * Badge trạng thái HTML
     */
    public function getStatusBadgeAttribute(): string
    {
        $config = match ($this->status) {
            'pending', 'new' => ['bg-warning text-dark', 'Chờ tiếp nhận', 'bi-hourglass-split'],
            'assigned' => ['bg-info text-dark', 'Đã phân công thợ', 'bi-person-check'],
            'measuring', 'consulting' => ['bg-primary text-white', 'Đang đo đạc tại nhà', 'bi-rulers'],
            'contacted' => ['bg-info text-white', 'Đã liên hệ', 'bi-telephone-inbound'],
            'quoted' => ['bg-secondary text-white', 'Đã báo giá', 'bi-receipt'],
            'completed' => ['bg-success text-white', 'Đã chốt may đo', 'bi-check-circle-fill'],
            'cancelled' => ['bg-danger text-white', 'Đã hủy lịch', 'bi-x-circle-fill'],
            default => ['bg-light text-dark border', $this->status ?? 'Chờ tiếp nhận', 'bi-info-circle'],
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
