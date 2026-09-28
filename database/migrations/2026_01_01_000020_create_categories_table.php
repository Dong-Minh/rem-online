<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Bảng categories: Danh mục sản phẩm
     * Hỗ trợ danh mục cha-con (parent_id) để phân loại linh hoạt:
     * - Theo phân khúc: Cao cấp, Phổ thông
     * - Theo không gian: Phòng khách, Phòng ngủ
     * - Theo phong cách: Hiện đại, Tối giản
     */
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('image')->nullable();
            $table->unsignedBigInteger('parent_id')->nullable(); // Danh mục cha (self-referential)
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->boolean('show_on_home')->default(false); // Hiển thị trên trang chủ
            $table->timestamps();

            // Foreign key tự tham chiếu
            $table->foreign('parent_id')->references('id')->on('categories')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};
