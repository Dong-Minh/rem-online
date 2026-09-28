<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Bảng pivot: promotion_products và promotion_categories
     * Một khuyến mãi có thể áp dụng cho nhiều sản phẩm và nhiều danh mục
     */
    public function up(): void
    {
        // Khuyến mãi áp dụng cho sản phẩm cụ thể
        Schema::create('promotion_products', function (Blueprint $table) {
            $table->foreignId('promotion_id')->constrained()->onDelete('cascade');
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->primary(['promotion_id', 'product_id']);
        });

        // Khuyến mãi áp dụng cho cả danh mục
        Schema::create('promotion_categories', function (Blueprint $table) {
            $table->foreignId('promotion_id')->constrained()->onDelete('cascade');
            $table->foreignId('category_id')->constrained()->onDelete('cascade');
            $table->primary(['promotion_id', 'category_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('promotion_categories');
        Schema::dropIfExists('promotion_products');
    }
};
