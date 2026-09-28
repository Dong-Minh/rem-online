<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Bảng reviews: Đánh giá sản phẩm
     * Bảng wishlists: Danh sách yêu thích
     */
    public function up(): void
    {
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('order_id')->nullable()->constrained()->onDelete('set null'); // Chỉ review nếu đã mua
            $table->tinyInteger('rating'); // 1-5 sao
            $table->text('comment')->nullable();
            $table->boolean('is_approved')->default(false); // Admin duyệt trước khi hiện
            $table->timestamps();

            // Mỗi user chỉ review 1 lần / 1 sản phẩm / 1 đơn hàng
            $table->unique(['user_id', 'product_id', 'order_id']);
        });

        Schema::create('wishlists', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->timestamp('created_at')->useCurrent();

            $table->unique(['user_id', 'product_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wishlists');
        Schema::dropIfExists('reviews');
    }
};
