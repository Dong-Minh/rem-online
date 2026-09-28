<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('consultations')) {
            return;
        }

        Schema::create('consultations', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique(); // CS-2026-XXXX
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('customer_name');
            $table->string('phone');
            $table->string('email')->nullable();
            $table->string('address');
            $table->string('province')->default('Hà Nội');
            $table->string('district')->nullable();
            $table->string('ward')->nullable();
            $table->date('preferred_date');
            $table->enum('preferred_time_slot', ['morning', 'afternoon', 'evening'])->default('morning'); // Sáng (8-12h), Chiều (13h30-17h30), Tối (18h-20h30)
            $table->json('curtain_types')->nullable(); // Các loại rèm khách quan tâm
            $table->string('estimated_windows')->nullable(); // Số lượng ô cửa (1-2 cửa, 3-5 cửa, Toàn bộ nhà)
            $table->text('notes')->nullable(); // Yêu cầu đặc biệt của khách
            $table->enum('status', ['pending', 'assigned', 'measuring', 'quoted', 'completed', 'cancelled'])->default('pending');
            $table->foreignId('assigned_staff_id')->nullable()->constrained('users')->nullOnDelete(); // Thợ kỹ thuật/NV phụ trách
            $table->text('admin_notes')->nullable(); // Ghi chú kết quả khảo sát nội bộ
            $table->decimal('quoted_amount', 15, 2)->nullable(); // Báo giá ước tính sau khi đo xong
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('consultations');
    }
};
