<?php

namespace Database\Seeders;

use App\Models\Address;
use App\Models\Order;
use App\Models\Product;
use App\Models\Review;
use App\Models\User;
use App\Models\Wishlist;
use Illuminate\Database\Seeder;

class ReviewSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $customer = User::where('email', 'customer@remonline.vn')->first();
        $admin = User::where('email', 'admin@remonline.vn')->first();
        $staff = User::where('email', 'staff@remonline.vn')->first();

        if (!$customer) {
            return;
        }

        // 1. SEED ĐỊA CHỈ NHẬN HÀNG (ADDRESS BOOK)
        Address::updateOrCreate(
            ['user_id' => $customer->id, 'phone' => '0904567890'],
            [
                'recipient_name' => 'Nguyễn Văn An (Khách Mẫu)',
                'province' => 'Hà Nội',
                'district' => 'Nam Từ Liêm',
                'ward' => 'Mỹ Đình 1',
                'address_detail' => 'Số 322/76 ngách 76 ngõ 322 Mỹ Đình, P. Mỹ Đình 1',
                'is_default' => true,
            ]
        );

        Address::updateOrCreate(
            ['user_id' => $customer->id, 'phone' => '0988776655'],
            [
                'recipient_name' => 'Nguyễn Văn An (Văn phòng)',
                'province' => 'Hà Nội',
                'district' => 'Cầu Giấy',
                'ward' => 'Dịch Vọng Hậu',
                'address_detail' => 'Tầng 8, Tòa nhà Keangnam Landmark 72, Đường Phạm Hùng',
                'is_default' => false,
            ]
        );

        // 2. SEED DANH SÁCH YÊU THÍCH (WISHLIST)
        $favoriteProducts = Product::take(3)->get();
        foreach ($favoriteProducts as $prod) {
            Wishlist::updateOrCreate([
                'user_id' => $customer->id,
                'product_id' => $prod->id,
            ]);
        }

        // 3. SEED ĐÁNH GIÁ SẢN PHẨM RÈM (REVIEWS)
        $products = Product::all();
        $sampleOrders = Order::all();

        $realisticComments = [
            [
                'rating' => 5,
                'comment' => 'Vải gấm rất dày dặn và sang trọng, đúng chuẩn cản sáng 100% như cam kết. Thợ đến đo đạc và lắp đặt tại nhà rất nhiệt tình, đường may thẳng tắp không tì vết!',
            ],
            [
                'rating' => 5,
                'comment' => 'Màu sắc bên ngoài còn đẹp và sắc nét hơn trên hình. Thanh ray trượt siêu êm ái, kéo nhẹ tay không nghe tiếng ồn. Rất hài lòng với dịch vụ may đo của shop.',
            ],
            [
                'rating' => 5,
                'comment' => 'Đặt may theo kích thước cửa sổ phòng ngủ 2.8m x 2.6m vừa khít từng milimet. Cản nhiệt tốt giúp phòng bật điều hòa mát rất nhanh.',
            ],
            [
                'rating' => 4,
                'comment' => 'Chất liệu vải đẹp, sờ mịn tay. Giao hàng và may đúng tiến độ cam kết trong 3 ngày. Sẽ ủng hộ thêm rèm cho phòng khách tầng 2.',
            ],
            [
                'rating' => 5,
                'comment' => 'Rèm cầu vồng Modero kéo rất êm, điều chỉnh lấy sáng siêu tiện. Nhà mình ở chung cư hướng Tây lắp xong phòng dịu hẳn.',
            ],
            [
                'rating' => 5,
                'comment' => 'Dịch vụ khảo sát tận nhà miễn phí rất chuyên nghiệp, mang theo cả bảng mẫu vải thực tế để chọn tận tay. 10/10 điểm cho chất lượng và phục vụ!',
            ],
        ];

        foreach ($products as $index => $prod) {
            $commentIndex = $index % count($realisticComments);
            $reviewData = $realisticComments[$commentIndex];
            $orderId = $sampleOrders->isNotEmpty() ? $sampleOrders->random()->id : null;

            Review::updateOrCreate(
                [
                    'product_id' => $prod->id,
                    'user_id' => $customer->id,
                ],
                [
                    'order_id' => $orderId,
                    'rating' => $reviewData['rating'],
                    'comment' => $reviewData['comment'],
                    'is_approved' => true,
                ]
            );

            // Thêm review thứ 2 từ nhân viên/khách khác nếu có
            if ($admin) {
                $secondIndex = ($commentIndex + 1) % count($realisticComments);
                $secondReview = $realisticComments[$secondIndex];
                Review::updateOrCreate(
                    [
                        'product_id' => $prod->id,
                        'user_id' => $admin->id,
                    ],
                    [
                        'order_id' => null,
                        'rating' => $secondReview['rating'],
                        'comment' => $secondReview['comment'],
                        'is_approved' => true,
                    ]
                );
            }
        }
    }
}
