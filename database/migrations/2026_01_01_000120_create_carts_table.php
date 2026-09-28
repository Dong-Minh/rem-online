<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Bảng carts và cart_items: Giỏ hàng
     *
     * Hỗ trợ cả khách chưa đăng nhập (session_id) và đã đăng nhập (user_id)
     * Khi đăng nhập, CartService sẽ merge session cart → user cart
     *
     * cart_items lưu đầy đủ: màu, kích thước, diện tích, giá tại thời điểm thêm vào giỏ
     */
    public function up(): void
    {
        Schema::create('carts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('session_id')->nullable()->index(); // Cho khách chưa đăng nhập
            $table->timestamps();
        });

        Schema::create('cart_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cart_id')->constrained()->onDelete('cascade');
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->foreignId('color_id')->nullable()->constrained()->onDelete('set null');

            // ============ KÍCH THƯỚC (đặc thù rèm) ============
            $table->decimal('width', 8, 2);   // Chiều rộng (m)
            $table->decimal('height', 8, 2);  // Chiều cao (m)
            $table->decimal('area', 10, 4);   // Diện tích (m²) = width × height

            // ============ GIÁ TẠI THỜI ĐIỂM THÊM VÀO GIỎ ============
            // Lưu lại để so sánh khi giá thay đổi
            $table->decimal('unit_price', 12, 2);
            $table->decimal('sale_unit_price', 12, 2)->nullable();

            $table->integer('quantity')->default(1);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cart_items');
        Schema::dropIfExists('carts');
    }
};
