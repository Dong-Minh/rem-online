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
        Schema::create('payment_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->string('gateway'); // Phân biệt cổng thanh toán: momo, cod, vnpay, etc.
            $table->string('gateway_order_id')->nullable()->index(); // ID đơn hàng từ cổng thanh toán
            $table->string('transaction_id')->nullable()->index(); // ID giao dịch do cổng thanh toán cấp
            $table->decimal('amount', 15, 2); // Số tiền giao dịch
            $table->string('status')->default('pending'); // pending, initiated, paid, completed, failed, canceled
            $table->integer('result_code')->nullable(); // Mã phản hồi kết quả từ cổng thanh toán (0: Thành công)
            $table->string('message')->nullable(); // Thông điệp phản hồi
            $table->json('request_payload')->nullable(); // Dữ liệu gửi sang gateway
            $table->json('response_payload')->nullable(); // Dữ liệu gateway phản hồi về
            $table->timestamp('paid_at')->nullable(); // Thời điểm thanh toán thành công
            $table->timestamps();

            $table->unique(['gateway', 'gateway_order_id']);
            $table->index(['order_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_transactions');
    }
};
