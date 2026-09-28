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
        Schema::table('consultations', function (Blueprint $table) {
            if (!Schema::hasColumn('consultations', 'code')) {
                $table->string('code')->nullable()->unique()->after('id');
            }
            if (!Schema::hasColumn('consultations', 'customer_name')) {
                $table->string('customer_name')->nullable()->after('user_id');
            }
            if (!Schema::hasColumn('consultations', 'address')) {
                $table->string('address')->nullable()->after('email');
            }
            if (!Schema::hasColumn('consultations', 'province')) {
                $table->string('province')->default('Hà Nội')->after('address');
            }
            if (!Schema::hasColumn('consultations', 'district')) {
                $table->string('district')->nullable()->after('province');
            }
            if (!Schema::hasColumn('consultations', 'ward')) {
                $table->string('ward')->nullable()->after('district');
            }
            if (!Schema::hasColumn('consultations', 'preferred_date')) {
                $table->date('preferred_date')->nullable()->after('ward');
            }
            if (!Schema::hasColumn('consultations', 'preferred_time_slot')) {
                $table->string('preferred_time_slot')->default('morning')->after('preferred_date');
            }
            if (!Schema::hasColumn('consultations', 'curtain_types')) {
                $table->json('curtain_types')->nullable()->after('preferred_time_slot');
            }
            if (!Schema::hasColumn('consultations', 'estimated_windows')) {
                $table->string('estimated_windows')->nullable()->after('curtain_types');
            }
            if (!Schema::hasColumn('consultations', 'notes')) {
                $table->text('notes')->nullable()->after('estimated_windows');
            }
            if (!Schema::hasColumn('consultations', 'assigned_staff_id')) {
                $table->foreignId('assigned_staff_id')->nullable()->after('status')->constrained('users')->nullOnDelete();
            }
            if (!Schema::hasColumn('consultations', 'admin_notes')) {
                $table->text('admin_notes')->nullable()->after('assigned_staff_id');
            }
            if (!Schema::hasColumn('consultations', 'quoted_amount')) {
                $table->decimal('quoted_amount', 15, 2)->nullable()->after('admin_notes');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Keep columns safely
    }
};
