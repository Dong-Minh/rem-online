<?php

namespace Database\Seeders;

use App\Models\Consultation;
use App\Models\User;
use Illuminate\Database\Seeder;

class ConsultationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $customer = User::where('email', 'customer@remonline.vn')->first();
        $staff = User::where('email', 'staff@remonline.vn')->first();
        $admin = User::where('email', 'admin@remonline.vn')->first();

        $sampleBookings = [
            [
                'code' => 'CS-2026-VNH01',
                'user_id' => $customer?->id,
                'customer_name' => 'Nguyễn Văn An (Khách Mẫu)',
                'phone' => '0904567890',
                'email' => 'customer@remonline.vn',
                'province' => 'Hà Nội',
                'district' => 'Nam Từ Liêm',
                'ward' => 'Tây Mỗ',
                'address' => 'P1204 Tòa R1.02 Vinhomes Smart City',
                'preferred_date' => now()->addDays(1)->format('Y-m-d'),
                'preferred_time_slot' => 'morning',
                'curtain_types' => ['Rèm Vải 2 Lớp Cao Cấp (Bỉ, Hàn Quốc)', 'Rèm Cầu Vồng Hàn Quốc Hiện Đại'],
                'estimated_windows' => '3 - 5 ô cửa (Căn hộ 2-3 phòng ngủ)',
                'notes' => 'Căn hộ hướng Tây đón nắng gắt, cần mang bảng mẫu vải chống nắng cản nhiệt 100% màu xám ghi.',
                'status' => 'assigned',
                'assigned_staff_id' => $staff?->id,
                'admin_notes' => 'Đã gọi điện xác nhận lúc 09h sáng, thợ chuẩn bị catalogue vải Bỉ Melbourne và Modero.',
                'quoted_amount' => null,
            ],
            [
                'code' => 'CS-2026-STL02',
                'user_id' => null,
                'customer_name' => 'Trần Thị Mai',
                'phone' => '0918889999',
                'email' => 'maitt@gmail.com',
                'province' => 'Hà Nội',
                'district' => 'Bắc Từ Liêm',
                'ward' => 'Xuân Tảo',
                'address' => 'Biệt thự H6 KĐT Starlake Tây Hồ Tây',
                'preferred_date' => now()->addDays(2)->format('Y-m-d'),
                'preferred_time_slot' => 'afternoon',
                'curtain_types' => ['Rèm Vải 2 Lớp Cao Cấp (Bỉ, Hàn Quốc)', 'Rèm Voan Thêu Nghệ Thuật', 'Rèm Tự Động Điều Khiển Thông Minh'],
                'estimated_windows' => 'Biệt thự / Villa cao cấp',
                'notes' => 'Trần phòng khách thông tầng cao 6.5m, cần tư vấn động cơ rèm tự động Somfy.',
                'status' => 'measuring',
                'assigned_staff_id' => $staff?->id,
                'admin_notes' => 'Thợ đang đo đạc ô cửa tại công trình biệt thự, mang thước laser chuyên dụng.',
                'quoted_amount' => null,
            ],
            [
                'code' => 'CS-2026-CGY03',
                'user_id' => null,
                'customer_name' => 'Lê Hoàng Nam',
                'phone' => '0936667788',
                'email' => 'namlh@fpt.com.vn',
                'province' => 'Hà Nội',
                'district' => 'Cầu Giấy',
                'ward' => 'Dịch Vọng Hậu',
                'address' => 'Số 45 Ngõ 12 Duy Tân, Tòa nhà FPT Tower',
                'preferred_date' => now()->subDays(1)->format('Y-m-d'),
                'preferred_time_slot' => 'morning',
                'curtain_types' => ['Rèm Cuốn Văn Phòng & Cản Nhiệt'],
                'estimated_windows' => 'Văn phòng / Tòa nhà doanh nghiệp',
                'notes' => 'Khảo sát 6 phòng làm việc và 1 phòng họp công ty.',
                'status' => 'completed',
                'assigned_staff_id' => $staff?->id,
                'admin_notes' => 'Đã đo xong 42m2 rèm cuốn trơn tráng bạc. Khách đã ký hợp đồng may đo ORD-2026-0001.',
                'quoted_amount' => 12800000,
            ],
            [
                'code' => 'CS-2026-HBT04',
                'user_id' => null,
                'customer_name' => 'Phạm Thu Trang',
                'phone' => '0977223344',
                'email' => 'thutrang.hn@gmail.com',
                'province' => 'Hà Nội',
                'district' => 'Hai Bà Trưng',
                'ward' => 'Vĩnh Tuy',
                'address' => 'Tầng 18 Tòa Park 08 Times City',
                'preferred_date' => now()->addDays(1)->format('Y-m-d'),
                'preferred_time_slot' => 'evening',
                'curtain_types' => ['Rèm Roman Xếp Lớp Tinh Tế', 'Rèm Cầu Vồng Hàn Quốc Hiện Đại'],
                'estimated_windows' => '1 - 2 ô cửa',
                'notes' => 'Khách chỉ ở nhà buổi tối sau 18h30.',
                'status' => 'pending',
                'assigned_staff_id' => null,
                'admin_notes' => 'Lịch hẹn mới đăng ký trực tuyến, cần gọi xác nhận trước 17h.',
                'quoted_amount' => null,
            ],
            [
                'code' => 'CS-2026-HDO05',
                'user_id' => null,
                'customer_name' => 'Hoàng Minh Tuấn',
                'phone' => '0983112233',
                'email' => 'tuanhm@vnpt.vn',
                'province' => 'Hà Nội',
                'district' => 'Hà Đông',
                'ward' => 'Phú La',
                'address' => 'Liền kề LK-12 Khu đô thị Văn Phú',
                'preferred_date' => now()->format('Y-m-d'),
                'preferred_time_slot' => 'morning',
                'curtain_types' => ['Rèm Vải 2 Lớp Cao Cấp (Bỉ, Hàn Quốc)'],
                'estimated_windows' => '3 - 5 ô cửa (Căn hộ 2-3 phòng ngủ)',
                'notes' => 'Yêu cầu thanh ray rèm sơn tĩnh điện màu vàng champagne.',
                'status' => 'quoted',
                'assigned_staff_id' => $staff?->id,
                'admin_notes' => 'Đã gửi bảng báo giá 7.450.000đ qua Zalo cho khách duyệt mẫu.',
                'quoted_amount' => 7450000,
            ],
        ];

        foreach ($sampleBookings as $bData) {
            Consultation::updateOrCreate(
                ['code' => $bData['code']],
                $bData
            );
        }
    }
}
