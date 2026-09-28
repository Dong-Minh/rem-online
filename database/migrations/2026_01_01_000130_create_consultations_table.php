<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Bảng consultations: Yêu cầu tư vấn
     * Đặc thù của cửa hàng rèm: khách hàng cần tư vấn về kích thước, màu sắc, loại vải
     */
    public function up(): void
    {
        Schema::create('consultations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->string('name');
            $table->string('phone', 20);
            $table->string('email')->nullable();
            $table->text('content');                          // Nội dung yêu cầu
            $table->string('curtain_type_interest')->nullable(); // Loại rèm quan tâm
            $table->decimal('estimated_width', 8, 2)->nullable();  // Kích thước dự kiến
            $table->decimal('estimated_height', 8, 2)->nullable();
            $table->string('preferred_contact_time')->nullable(); // Thời gian muốn được liên hệ
            $table->enum('status', [
                'new',        // Mới
                'received',   // Đã tiếp nhận
                'consulting', // Đang tư vấn
                'contacted',  // Đã liên hệ
                'completed',  // Hoàn thành
                'cancelled',  // Hủy
            ])->default('new');
            $table->text('admin_note')->nullable(); // Ghi chú nội bộ của admin
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('consultations');
    }
};
