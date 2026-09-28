<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Bổ sung các cột tích hợp Giao Hàng Nhanh (GHN) vào bảng orders
     */
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('shipping_status')->default('not_shipped')->after('status');
            $table->string('ghn_order_code')->nullable()->index()->after('shipping_status');
            $table->integer('ghn_total_fee')->default(0)->after('ghn_order_code');
            $table->integer('to_district_id')->nullable()->after('ghn_total_fee');
            $table->string('to_ward_code')->nullable()->after('to_district_id');
            $table->integer('to_province_id')->nullable()->after('to_ward_code');
            $table->string('recipient_email')->nullable()->after('recipient_phone');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'shipping_status',
                'ghn_order_code',
                'ghn_total_fee',
                'to_district_id',
                'to_ward_code',
                'to_province_id',
                'recipient_email',
            ]);
        });
    }
};
