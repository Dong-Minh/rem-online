<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Bảng products: Sản phẩm rèm
     *
     * Điểm đặc biệt: Giá tính theo m² (unit_price / m²)
     * Không có giá cố định — giá = diện tích × đơn giá/m²
     *
     * Giới hạn kích thước: min/max width/height để tránh đặt rèm
     * kích thước không hợp lệ với cửa hàng
     */
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('sku')->unique();          // Mã sản phẩm
            $table->string('slug')->unique();          // URL-friendly name
            $table->text('short_description')->nullable();
            $table->longText('description')->nullable();

            // ============ GIÁ THEO M² ============
            $table->decimal('unit_price', 12, 2);     // Giá gốc / m²
            $table->decimal('sale_price', 12, 2)->nullable(); // Giá khuyến mãi / m²

            // ============ GIỚI HẠN KÍCH THƯỚC ============
            $table->decimal('min_width', 5, 2)->nullable();   // Chiều rộng tối thiểu (m)
            $table->decimal('max_width', 5, 2)->nullable();   // Chiều rộng tối đa (m)
            $table->decimal('min_height', 5, 2)->nullable();  // Chiều cao tối thiểu (m)
            $table->decimal('max_height', 5, 2)->nullable();  // Chiều cao tối đa (m)

            // ============ HÌNH ẢNH ============
            $table->string('main_image')->nullable();  // Ảnh chính

            // ============ THÔNG SỐ KỸ THUẬT ============
            $table->string('material')->nullable();    // Chất liệu: Vải, Lụa, Polyester...
            $table->string('style')->nullable();       // Phong cách: Hiện đại, Tối giản...
            $table->string('curtain_type')->nullable(); // Kiểu rèm: Rèm vải, Rèm cuốn, Rèm cầu vồng...

            // ============ TRẠNG THÁI ============
            $table->enum('stock_status', ['in_stock', 'out_of_stock', 'hidden'])->default('in_stock');

            // ============ GẮN NHÃN ĐẶC BIỆT ============
            $table->boolean('is_hot')->default(false);      // Sản phẩm HOT
            $table->boolean('is_trending')->default(false); // Sản phẩm THỊNH HÀNH
            $table->boolean('is_featured')->default(false); // Sản phẩm NỔI BẬT
            $table->boolean('is_new')->default(true);       // Sản phẩm MỚI

            // ============ THỐNG KÊ ============
            $table->unsignedInteger('view_count')->default(0);
            $table->unsignedInteger('sold_count')->default(0);

            $table->timestamps();
            $table->softDeletes(); // Xóa mềm — không xóa thật khỏi DB
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
