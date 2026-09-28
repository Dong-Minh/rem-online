<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Thêm foreign key order_id vào voucher_usages
     * Phải chạy SAU khi orders table đã tồn tại
     */
    public function up(): void
    {
        Schema::table('voucher_usages', function (Blueprint $table) {
            $table->foreign('order_id')->references('id')->on('orders')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('voucher_usages', function (Blueprint $table) {
            $table->dropForeign(['order_id']);
        });
    }
};
