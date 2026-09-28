<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Bảng addresses: Địa chỉ nhận hàng của khách hàng
     * Một user có nhiều địa chỉ, có thể chọn 1 địa chỉ mặc định
     */
    public function up(): void
    {
        Schema::create('addresses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('recipient_name');
            $table->string('phone', 20);
            $table->string('province');       // Tỉnh/Thành phố
            $table->string('district');       // Quận/Huyện
            $table->string('ward');           // Phường/Xã
            $table->string('address_detail'); // Số nhà, tên đường
            $table->boolean('is_default')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('addresses');
    }
};
