<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Bảng product_color: Quan hệ nhiều-nhiều giữa Product và Color
     * Kèm theo stock_quantity (tồn kho theo màu)
     *
     * Ví dụ: Rèm vải A có màu Trắng (còn 50m) và màu Kem (hết hàng)
     */
    public function up(): void
    {
        Schema::create('product_color', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->foreignId('color_id')->constrained()->onDelete('cascade');
            // Tồn kho theo màu (đơn vị: mét vải hoặc số lượng tấm)
            $table->integer('stock_quantity')->default(0);
            $table->timestamps();

            $table->unique(['product_id', 'color_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_color');
    }
};
