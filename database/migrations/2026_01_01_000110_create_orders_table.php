<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Bảng orders và order_items: Đơn hàng
     *
     * QUAN TRỌNG: order_items lưu SNAPSHOT thông tin tại thời điểm đặt hàng
     * (tên sản phẩm, giá, màu, kích thước) để tránh bị ảnh hưởng khi admin sửa sau
     *
     * Giá tính theo m²:
     * item_total = area × effective_price × quantity
     *            = (width × height) × (sale_unit_price ?? unit_price) × quantity
     */
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_code')->unique(); // Mã đơn: ORD-20260907-0001
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('address_id')->nullable()->constrained()->onDelete('set null');

            // Snapshot thông tin giao hàng (để tránh mất khi address bị xóa)
            $table->string('recipient_name');
            $table->string('recipient_phone', 20);
            $table->text('shipping_address');

            // Tài chính
            $table->decimal('subtotal', 12, 2);            // Tổng trước giảm
            $table->decimal('promotion_discount', 12, 2)->default(0); // Giảm từ khuyến mãi
            $table->decimal('voucher_discount', 12, 2)->default(0);   // Giảm từ voucher
            $table->foreignId('voucher_id')->nullable()->constrained()->onDelete('set null');
            $table->decimal('shipping_fee', 12, 2)->default(0);
            $table->decimal('total', 12, 2);               // Tổng thanh toán

            $table->string('payment_method')->default('cod');
            $table->string('payment_status')->default('pending');

            // Trạng thái đơn hàng
            $table->enum('status', [
                'pending',    // Chờ xác nhận
                'confirmed',  // Đã xác nhận
                'preparing',  // Đang chuẩn bị
                'shipping',   // Đang giao
                'delivered',  // Đã giao
                'completed',  // Hoàn thành
                'cancelled',  // Đã hủy
            ])->default('pending');

            $table->text('note')->nullable(); // Ghi chú của khách
            $table->timestamps();
        });

        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->onDelete('cascade');

            // Product reference (nullable vì product có thể bị xóa mềm)
            $table->foreignId('product_id')->nullable()->constrained()->onDelete('set null');
            $table->string('product_name');   // Snapshot tên sản phẩm
            $table->string('product_sku');    // Snapshot SKU

            // Color reference
            $table->foreignId('color_id')->nullable()->constrained()->onDelete('set null');
            $table->string('color_name')->nullable(); // Snapshot tên màu

            // ============ KÍCH THƯỚC RÈM (đặc thù) ============
            $table->decimal('width', 8, 2);   // Chiều rộng (m)
            $table->decimal('height', 8, 2);  // Chiều cao (m)
            $table->decimal('area', 10, 4);   // Diện tích = width × height (m²)

            // ============ GIÁ SNAPSHOT ============
            $table->decimal('unit_price', 12, 2);             // Giá gốc / m² tại thời điểm đặt
            $table->decimal('sale_unit_price', 12, 2)->nullable(); // Giá KM / m²
            $table->integer('quantity')->default(1);
            $table->decimal('item_total', 12, 2); // Tổng tiền item = area × price × qty

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_items');
        Schema::dropIfExists('orders');
    }
};
