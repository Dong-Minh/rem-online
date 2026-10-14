<?php

use App\Models\Consultation;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        try {
            // 1. Tạo bảng nếu chưa từng tồn tại
            if (!Schema::hasTable('consultations')) {
                Schema::create('consultations', function (Blueprint $table) {
                    $table->id();
                    $table->string('code')->nullable()->unique();
                    $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
                    $table->string('customer_name')->nullable();
                    $table->string('name')->nullable();
                    $table->string('phone', 50)->nullable();
                    $table->string('email')->nullable();
                    $table->string('address')->nullable();
                    $table->string('province')->default('Hà Nội');
                    $table->string('district')->nullable();
                    $table->string('ward')->nullable();
                    $table->date('preferred_date')->nullable();
                    $table->string('preferred_time_slot')->default('morning');
                    $table->json('curtain_types')->nullable();
                    $table->string('curtain_type_interest')->nullable();
                    $table->string('estimated_windows')->nullable();
                    $table->decimal('estimated_width', 8, 2)->nullable();
                    $table->decimal('estimated_height', 8, 2)->nullable();
                    $table->string('preferred_contact_time')->nullable();
                    $table->text('notes')->nullable();
                    $table->text('content')->nullable();
                    $table->string('status', 50)->default('pending');
                    $table->foreignId('assigned_staff_id')->nullable()->constrained('users')->nullOnDelete();
                    $table->text('admin_notes')->nullable();
                    $table->text('admin_note')->nullable();
                    $table->decimal('quoted_amount', 15, 2)->nullable();
                    $table->timestamps();
                });
            } else {
                // 2. Nếu bảng đã tồn tại, bổ sung các cột còn thiếu
                Schema::table('consultations', function (Blueprint $table) {
                    if (!Schema::hasColumn('consultations', 'code')) {
                        $table->string('code')->nullable();
                    }
                    if (!Schema::hasColumn('consultations', 'customer_name')) {
                        $table->string('customer_name')->nullable();
                    }
                    if (!Schema::hasColumn('consultations', 'name')) {
                        $table->string('name')->nullable();
                    }
                    if (!Schema::hasColumn('consultations', 'phone')) {
                        $table->string('phone', 50)->nullable();
                    }
                    if (!Schema::hasColumn('consultations', 'email')) {
                        $table->string('email')->nullable();
                    }
                    if (!Schema::hasColumn('consultations', 'address')) {
                        $table->string('address')->nullable();
                    }
                    if (!Schema::hasColumn('consultations', 'province')) {
                        $table->string('province')->default('Hà Nội');
                    }
                    if (!Schema::hasColumn('consultations', 'district')) {
                        $table->string('district')->nullable();
                    }
                    if (!Schema::hasColumn('consultations', 'ward')) {
                        $table->string('ward')->nullable();
                    }
                    if (!Schema::hasColumn('consultations', 'preferred_date')) {
                        $table->date('preferred_date')->nullable();
                    }
                    if (!Schema::hasColumn('consultations', 'preferred_time_slot')) {
                        $table->string('preferred_time_slot')->default('morning');
                    }
                    if (!Schema::hasColumn('consultations', 'curtain_types')) {
                        $table->json('curtain_types')->nullable();
                    }
                    if (!Schema::hasColumn('consultations', 'curtain_type_interest')) {
                        $table->string('curtain_type_interest')->nullable();
                    }
                    if (!Schema::hasColumn('consultations', 'estimated_windows')) {
                        $table->string('estimated_windows')->nullable();
                    }
                    if (!Schema::hasColumn('consultations', 'notes')) {
                        $table->text('notes')->nullable();
                    }
                    if (!Schema::hasColumn('consultations', 'content')) {
                        $table->text('content')->nullable();
                    }
                    if (!Schema::hasColumn('consultations', 'assigned_staff_id')) {
                        $table->foreignId('assigned_staff_id')->nullable()->constrained('users')->nullOnDelete();
                    }
                    if (!Schema::hasColumn('consultations', 'admin_notes')) {
                        $table->text('admin_notes')->nullable();
                    }
                    if (!Schema::hasColumn('consultations', 'admin_note')) {
                        $table->text('admin_note')->nullable();
                    }
                    if (!Schema::hasColumn('consultations', 'quoted_amount')) {
                        $table->decimal('quoted_amount', 15, 2)->nullable();
                    }
                });

                // 3. Chuẩn hóa các cột strict mode trong MySQL
                $driver = DB::getDriverName();
                if ($driver === 'mysql') {
                    try {
                        DB::statement("ALTER TABLE `consultations` MODIFY COLUMN `name` VARCHAR(255) NULL");
                    } catch (\Throwable $e) {}

                    try {
                        DB::statement("ALTER TABLE `consultations` MODIFY COLUMN `content` TEXT NULL");
                    } catch (\Throwable $e) {}

                    try {
                        DB::statement("ALTER TABLE `consultations` MODIFY COLUMN `status` VARCHAR(50) NOT NULL DEFAULT 'pending'");
                    } catch (\Throwable $e) {}

                    try {
                        DB::statement("ALTER TABLE `consultations` MODIFY COLUMN `phone` VARCHAR(50) NULL");
                    } catch (\Throwable $e) {}

                    try {
                        DB::statement("ALTER TABLE `consultations` MODIFY COLUMN `address` VARCHAR(255) NULL");
                    } catch (\Throwable $e) {}
                }
            }

            // 4. Đồng bộ dữ liệu giữa các cặp cột tương thích
            try {
                DB::statement("UPDATE `consultations` SET `customer_name` = `name` WHERE (`customer_name` IS NULL OR `customer_name` = '') AND `name` IS NOT NULL AND `name` != ''");
                DB::statement("UPDATE `consultations` SET `name` = `customer_name` WHERE (`name` IS NULL OR `name` = '') AND `customer_name` IS NOT NULL AND `customer_name` != ''");
                DB::statement("UPDATE `consultations` SET `notes` = `content` WHERE (`notes` IS NULL OR `notes` = '') AND `content` IS NOT NULL AND `content` != ''");
                DB::statement("UPDATE `consultations` SET `content` = `notes` WHERE (`content` IS NULL OR `content` = '') AND `notes` IS NOT NULL AND `notes` != ''");
                DB::statement("UPDATE `consultations` SET `status` = 'pending' WHERE `status` = 'new'");
            } catch (\Throwable $ex) {
                Log::warning('Data sync in fix_and_normalize_consultations_table: ' . $ex->getMessage());
            }

            // 5. Tự động nạp dữ liệu mẫu lịch hẹn nếu bảng đang trống (phục vụ demo và test admin)
            if (Consultation::count() === 0) {
                $staff = User::where('role', 'staff')->orWhere('role', 'admin')->first();
                $customer = User::where('role', 'customer')->first();

                $samples = [
                    [
                        'code' => 'CS-2026-VNH01',
                        'user_id' => $customer?->id,
                        'customer_name' => 'Nguyễn Văn An (Khách Mẫu)',
                        'name' => 'Nguyễn Văn An (Khách Mẫu)',
                        'phone' => '0904567890',
                        'email' => 'customer@remonline.vn',
                        'province' => 'Hà Nội',
                        'district' => 'Nam Từ Liêm',
                        'ward' => 'Tây Mỗ',
                        'address' => 'P1204 Tòa R1.02 Vinhomes Smart City',
                        'preferred_date' => now()->addDays(1)->format('Y-m-d'),
                        'preferred_time_slot' => 'morning',
                        'curtain_types' => ['Rèm Vải 2 Lớp Cao Cấp (Bỉ, Hàn Quốc)', 'Rèm Cầu Vồng Hàn Quốc Hiện Đại'],
                        'curtain_type_interest' => 'Rèm Vải 2 Lớp Cao Cấp, Rèm Cầu Vồng Hàn Quốc',
                        'estimated_windows' => '3 - 5 ô cửa (Căn hộ 2-3 phòng ngủ)',
                        'notes' => 'Căn hộ hướng Tây đón nắng gắt, cần mang bảng mẫu vải chống nắng cản nhiệt 100% màu xám ghi.',
                        'content' => 'Căn hộ hướng Tây đón nắng gắt, cần mang bảng mẫu vải chống nắng cản nhiệt 100% màu xám ghi.',
                        'status' => 'assigned',
                        'assigned_staff_id' => $staff?->id,
                        'admin_notes' => 'Đã gọi điện xác nhận lúc 09h sáng, thợ chuẩn bị catalogue vải Bỉ Melbourne và Modero.',
                        'admin_note' => 'Đã gọi điện xác nhận lúc 09h sáng, thợ chuẩn bị catalogue vải Bỉ Melbourne và Modero.',
                        'quoted_amount' => null,
                        'created_at' => now()->subHours(5),
                        'updated_at' => now()->subHours(2),
                    ],
                    [
                        'code' => 'CS-2026-STL02',
                        'user_id' => null,
                        'customer_name' => 'Trần Thị Mai',
                        'name' => 'Trần Thị Mai',
                        'phone' => '0918889999',
                        'email' => 'maitt@gmail.com',
                        'province' => 'Hà Nội',
                        'district' => 'Bắc Từ Liêm',
                        'ward' => 'Xuân Tảo',
                        'address' => 'Biệt thự H6 KĐT Starlake Tây Hồ Tây',
                        'preferred_date' => now()->addDays(2)->format('Y-m-d'),
                        'preferred_time_slot' => 'afternoon',
                        'curtain_types' => ['Rèm Vải 2 Lớp Cao Cấp (Bỉ, Hàn Quốc)', 'Rèm Voan Thêu Nghệ Thuật', 'Rèm Tự Động Điều Khiển Thông Minh'],
                        'curtain_type_interest' => 'Rèm Vải 2 Lớp, Rèm Voan Thêu, Rèm Tự Động',
                        'estimated_windows' => 'Biệt thự / Villa cao cấp',
                        'notes' => 'Trần phòng khách thông tầng cao 6.5m, cần tư vấn động cơ rèm tự động Somfy.',
                        'content' => 'Trần phòng khách thông tầng cao 6.5m, cần tư vấn động cơ rèm tự động Somfy.',
                        'status' => 'measuring',
                        'assigned_staff_id' => $staff?->id,
                        'admin_notes' => 'Thợ đang đo đạc ô cửa tại công trình biệt thự, mang thước laser chuyên dụng.',
                        'admin_note' => 'Thợ đang đo đạc ô cửa tại công trình biệt thự, mang thước laser chuyên dụng.',
                        'quoted_amount' => null,
                        'created_at' => now()->subHours(8),
                        'updated_at' => now()->subHours(1),
                    ],
                    [
                        'code' => 'CS-2026-CGY03',
                        'user_id' => null,
                        'customer_name' => 'Lê Hoàng Nam',
                        'name' => 'Lê Hoàng Nam',
                        'phone' => '0936667788',
                        'email' => 'namlh@fpt.com.vn',
                        'province' => 'Hà Nội',
                        'district' => 'Cầu Giấy',
                        'ward' => 'Dịch Vọng Hậu',
                        'address' => 'Số 45 Ngõ 12 Duy Tân, Tòa nhà FPT Tower',
                        'preferred_date' => now()->subDays(1)->format('Y-m-d'),
                        'preferred_time_slot' => 'morning',
                        'curtain_types' => ['Rèm Cuốn Văn Phòng & Cản Nhiệt'],
                        'curtain_type_interest' => 'Rèm Cuốn Văn Phòng & Cản Nhiệt',
                        'estimated_windows' => 'Văn phòng / Tòa nhà doanh nghiệp',
                        'notes' => 'Khảo sát 6 phòng làm việc và 1 phòng họp công ty.',
                        'content' => 'Khảo sát 6 phòng làm việc và 1 phòng họp công ty.',
                        'status' => 'completed',
                        'assigned_staff_id' => $staff?->id,
                        'admin_notes' => 'Đã đo xong 42m2 rèm cuốn trơn tráng bạc. Khách đã ký hợp đồng may đo.',
                        'admin_note' => 'Đã đo xong 42m2 rèm cuốn trơn tráng bạc. Khách đã ký hợp đồng may đo.',
                        'quoted_amount' => 12800000,
                        'created_at' => now()->subDays(2),
                        'updated_at' => now()->subDay(),
                    ],
                    [
                        'code' => 'CS-2026-HBT04',
                        'user_id' => null,
                        'customer_name' => 'Phạm Thu Trang',
                        'name' => 'Phạm Thu Trang',
                        'phone' => '0977223344',
                        'email' => 'thutrang.hn@gmail.com',
                        'province' => 'Hà Nội',
                        'district' => 'Hai Bà Trưng',
                        'ward' => 'Vĩnh Tuy',
                        'address' => 'Tầng 18 Tòa Park 08 Times City',
                        'preferred_date' => now()->addDays(1)->format('Y-m-d'),
                        'preferred_time_slot' => 'evening',
                        'curtain_types' => ['Rèm Roman Xếp Lớp Tinh Tế', 'Rèm Cầu Vồng Hàn Quốc Hiện Đại'],
                        'curtain_type_interest' => 'Rèm Roman, Rèm Cầu Vồng',
                        'estimated_windows' => '1 - 2 ô cửa',
                        'notes' => 'Khách chỉ ở nhà buổi tối sau 18h30.',
                        'content' => 'Khách chỉ ở nhà buổi tối sau 18h30.',
                        'status' => 'pending',
                        'assigned_staff_id' => null,
                        'admin_notes' => 'Lịch hẹn mới đăng ký trực tuyến, cần gọi xác nhận trước 17h.',
                        'admin_note' => 'Lịch hẹn mới đăng ký trực tuyến, cần gọi xác nhận trước 17h.',
                        'quoted_amount' => null,
                        'created_at' => now()->subMinutes(25),
                        'updated_at' => now()->subMinutes(25),
                    ],
                    [
                        'code' => 'CS-2026-HDO05',
                        'user_id' => null,
                        'customer_name' => 'Hoàng Minh Tuấn',
                        'name' => 'Hoàng Minh Tuấn',
                        'phone' => '0983112233',
                        'email' => 'tuanhm@vnpt.vn',
                        'province' => 'Hà Nội',
                        'district' => 'Hà Đông',
                        'ward' => 'Phú La',
                        'address' => 'Liền kề LK-12 Khu đô thị Văn Phú',
                        'preferred_date' => now()->format('Y-m-d'),
                        'preferred_time_slot' => 'morning',
                        'curtain_types' => ['Rèm Vải 2 Lớp Cao Cấp (Bỉ, Hàn Quốc)'],
                        'curtain_type_interest' => 'Rèm Vải 2 Lớp Cao Cấp',
                        'estimated_windows' => '3 - 5 ô cửa (Căn hộ 2-3 phòng ngủ)',
                        'notes' => 'Yêu cầu thanh ray rèm sơn tĩnh điện màu vàng champagne.',
                        'content' => 'Yêu cầu thanh ray rèm sơn tĩnh điện màu vàng champagne.',
                        'status' => 'quoted',
                        'assigned_staff_id' => $staff?->id,
                        'admin_notes' => 'Đã gửi bảng báo giá 7.450.000đ qua Zalo cho khách duyệt mẫu.',
                        'admin_note' => 'Đã gửi bảng báo giá 7.450.000đ qua Zalo cho khách duyệt mẫu.',
                        'quoted_amount' => 7450000,
                        'created_at' => now()->subHours(12),
                        'updated_at' => now()->subHours(3),
                    ],
                ];

                foreach ($samples as $sample) {
                    try {
                        Consultation::updateOrCreate(['code' => $sample['code']], $sample);
                    } catch (\Throwable $e) {
                        Log::warning('Seeding sample consultation failed: ' . $e->getMessage());
                    }
                }
            }
        } catch (\Throwable $e) {
            Log::error('Migration fix_and_normalize_consultations_table failed: ' . $e->getMessage());
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Safe keep
    }
};
