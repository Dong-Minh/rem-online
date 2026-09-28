<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Bảng colors: Màu sắc của rèm
     * Admin quản lý danh sách màu, sản phẩm có thể có nhiều màu
     */
    public function up(): void
    {
        Schema::create('colors', function (Blueprint $table) {
            $table->id();
            $table->string('name');             // Tên màu: Trắng, Kem, Xám...
            $table->string('hex_code', 10)->nullable(); // Mã màu hex: #FFFFFF
            $table->string('image')->nullable(); // Ảnh mẫu vải (nếu cần)
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('colors');
    }
};
