<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Bảng promotions: Chương trình khuyến mãi
     * Có thể áp dụng cho sản phẩm cụ thể hoặc danh mục
     */
    public function up(): void
    {
        Schema::create('promotions', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->enum('discount_type', ['percent', 'fixed']); // % hoặc tiền cố định
            $table->decimal('discount_value', 10, 2);
            $table->decimal('min_order_amount', 12, 2)->default(0); // Đơn tối thiểu
            $table->datetime('starts_at');
            $table->datetime('ends_at');
            $table->integer('usage_limit')->nullable(); // Tổng số lần dùng (null = không giới hạn)
            $table->integer('used_count')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('promotions');
    }
};
