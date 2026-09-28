<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Bảng vouchers: Mã giảm giá
     * Giống mô hình Shopee ở mức phù hợp môn học
     *
     * Hỗ trợ:
     * - Giảm % hoặc tiền cố định
     * - Giảm tối đa (cho loại %)
     * - Đơn hàng tối thiểu
     * - Giới hạn số lần dùng (tổng + mỗi user)
     * - Thời gian hiệu lực
     */
    public function up(): void
    {
        Schema::create('vouchers', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();       // Mã voucher: REMGIAM50
            $table->text('description')->nullable();
            $table->enum('discount_type', ['percent', 'fixed']);
            $table->decimal('discount_value', 10, 2);
            $table->decimal('max_discount_amount', 12, 2)->nullable(); // Giảm tối đa (cho loại %)
            $table->decimal('min_order_amount', 12, 2)->default(0);
            $table->integer('usage_limit')->nullable();    // Tổng số lần dùng
            $table->integer('usage_per_user')->default(1); // Mỗi user dùng tối đa
            $table->datetime('starts_at');
            $table->datetime('ends_at');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('voucher_usages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('voucher_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('order_id')->nullable(); // Sẽ có sau khi order được tạo
            $table->decimal('discount_amount', 12, 2); // Số tiền thực tế đã giảm
            $table->timestamp('used_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('voucher_usages');
        Schema::dropIfExists('vouchers');
    }
};
