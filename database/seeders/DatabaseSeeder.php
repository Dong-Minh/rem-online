<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Color;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. TÀI KHOẢN MẪU ĐỂ TEST
        $users = [
            [
                'name' => 'Super Administrator',
                'email' => 'superadmin@remonline.vn',
                'phone' => '0901234567',
                'role' => 'super_admin',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'is_active' => true,
            ],
            [
                'name' => 'Quản Trị Viên (Admin)',
                'email' => 'admin@remonline.vn',
                'phone' => '0902345678',
                'role' => 'admin',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'is_active' => true,
            ],
            [
                'name' => 'Nhân Viên Bán Hàng',
                'email' => 'staff@remonline.vn',
                'phone' => '0903456789',
                'role' => 'staff',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'is_active' => true,
            ],
            [
                'name' => 'Khách Hàng Mẫu',
                'email' => 'customer@remonline.vn',
                'phone' => '0904567890',
                'role' => 'customer',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'is_active' => true,
            ],
            [
                'name' => 'Khách Chưa Xác Thực',
                'email' => 'unverified@remonline.vn',
                'phone' => '0905678901',
                'role' => 'customer',
                'password' => Hash::make('password'),
                'email_verified_at' => null,
                'is_active' => true,
            ],
        ];

        foreach ($users as $userData) {
            User::updateOrCreate(['email' => $userData['email']], $userData);
        }

        // Tự động nạp tài khoản admin cấu hình qua biến môi trường Render nếu có
        $this->call(AdminUserSeeder::class);

        // 2. MÀU SẮC RÈM MẪU
        $colors = [
            ['name' => 'Trắng Tinh Khôi', 'hex_code' => '#FFFFFF', 'is_active' => true],
            ['name' => 'Kem Sữa Sang Trọng', 'hex_code' => '#FDFBF7', 'is_active' => true],
            ['name' => 'Xám Ghi Hiện Đại', 'hex_code' => '#9E9E9E', 'is_active' => true],
            ['name' => 'Xám Đậm Cản Sáng', 'hex_code' => '#374151', 'is_active' => true],
            ['name' => 'Xanh Pastel Dịu Mát', 'hex_code' => '#93C5FD', 'is_active' => true],
            ['name' => 'Nâu Cafe Ấm Áp', 'hex_code' => '#78350F', 'is_active' => true],
            ['name' => 'Vàng Be Hoàng Gia', 'hex_code' => '#D97706', 'is_active' => true],
        ];

        $colorModels = [];
        foreach ($colors as $c) {
            $colorModels[] = Color::updateOrCreate(['name' => $c['name']], $c);
        }

        // 3. DANH MỤC RÈM MẪU
        $categories = [
            [
                'name' => 'Rèm Vải Cao Cấp',
                'slug' => 'rem-vai-cao-cap',
                'description' => 'Chất liệu vải Bỉ & Hàn Quốc nhập khẩu, cản sáng 100%, dệt mật độ cao chống bám bụi.',
                'image' => 'https://images.unsplash.com/photo-1513694203232-719a280e022f?q=80&w=800&auto=format&fit=crop',
                'show_on_home' => true,
                'sort_order' => 1
            ],
            [
                'name' => 'Rèm Phòng Khách',
                'slug' => 'rem-phong-khach',
                'description' => 'Điểm nhấn thẩm mỹ hoàn hảo cho không gian sinh hoạt gia đình, tạo vẻ sang trọng đẳng cấp.',
                'image' => 'https://images.unsplash.com/photo-1615874959474-d609969a20ed?q=80&w=800&auto=format&fit=crop',
                'show_on_home' => true,
                'sort_order' => 2
            ],
            [
                'name' => 'Rèm Phòng Ngủ',
                'slug' => 'rem-phong-ngu',
                'description' => 'Cản sáng cách âm cách nhiệt tuyệt đối, mang lại không gian tĩnh lặng và giấc ngủ êm ái.',
                'image' => 'https://images.unsplash.com/photo-1540518614846-7ede433c4ef2?q=80&w=800&auto=format&fit=crop',
                'show_on_home' => true,
                'sort_order' => 3
            ],
            [
                'name' => 'Rèm Cầu Vồng Hàn Quốc',
                'slug' => 'rem-cau-vong-han-quoc',
                'description' => 'Thiết kế xen kẽ dải sáng tối hiện đại, điều chỉnh ánh sáng linh hoạt, dễ lau chùi.',
                'image' => 'https://images.unsplash.com/photo-1507089947368-19c1da9775ae?q=80&w=800&auto=format&fit=crop',
                'show_on_home' => true,
                'sort_order' => 4
            ],
            [
                'name' => 'Rèm Cuốn Văn Phòng',
                'slug' => 'rem-cuon-van-phong',
                'description' => 'Thiết kế tối giản gọn gàng, độ bền bỉ vượt trội, chống cháy nhẹ, giá thành tối ưu.',
                'image' => 'https://images.unsplash.com/photo-1497366216548-37526070297c?q=80&w=800&auto=format&fit=crop',
                'show_on_home' => true,
                'sort_order' => 5
            ],
            [
                'name' => 'Rèm Chống Nắng Cách Nhiệt',
                'slug' => 'rem-chong-nang-cach-nhiet',
                'description' => 'Công nghệ dệt phủ cao su non cản nhiệt tới 90%, tiết kiệm điện năng điều hòa.',
                'image' => 'https://images.unsplash.com/photo-1616486338812-3dadae4b4ace?q=80&w=800&auto=format&fit=crop',
                'show_on_home' => true,
                'sort_order' => 6
            ],
            [
                'name' => 'Rèm Biệt Thự & Chung Cư',
                'slug' => 'rem-biet-thu-chung-cu',
                'description' => 'Mẫu rèm 2 lớp voan thêu tinh tế dành riêng cho các căn hộ cao cấp và biệt thự.',
                'image' => 'https://images.unsplash.com/photo-1600585154340-be6161a56a0c?q=80&w=800&auto=format&fit=crop',
                'show_on_home' => false,
                'sort_order' => 7
            ],
        ];

        $categoryModels = [];
        foreach ($categories as $cat) {
            $categoryModels[$cat['slug']] = Category::updateOrCreate(['slug' => $cat['slug']], $cat);
        }

        // 4. SẢN PHẨM RÈM MẪU (35 sản phẩm chia đều 7 danh mục)
        $this->call(ProductSeeder::class);

        // 5. KHỞI TẠO MÃ GIẢM GIÁ (VOUCHERS)
        $this->call(VoucherSeeder::class);

        // 6. KHỞI TẠO ĐƠN HÀNG RÈM MẪU
        $this->call(OrderSeeder::class);

        // 7. KHỞI TẠO ĐÁNH GIÁ, SỔ ĐỊA CHỈ & YÊU THÍCH
        $this->call(ReviewSeeder::class);

        // 8. KHỞI TẠO LỊCH HẸN KHẢO SÁT & ĐO ĐẠC TẬN NHÀ
        $this->call(ConsultationSeeder::class);
    }
}
