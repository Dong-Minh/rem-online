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
        try {
            Schema::table('consultations', function (Blueprint $table) {
                if (!Schema::hasColumn('consultations', 'code')) {
                    $table->string('code')->nullable();
                }
                if (!Schema::hasColumn('consultations', 'customer_name')) {
                    $table->string('customer_name')->nullable();
                }
                if (!Schema::hasColumn('consultations', 'address')) {
                    $table->string('address')->nullable();
                }
                if (!Schema::hasColumn('consultations', 'province')) {
                    $table->string('province')->default('Hà Nội');
                }
                if (!Schema::hasColumn('consultations', 'district')) {
                    $table->string('district')->nullable();
                }
                if (!Schema::hasColumn('consultations', 'ward')) {
                    $table->string('ward')->nullable();
                }
                if (!Schema::hasColumn('consultations', 'preferred_date')) {
                    $table->date('preferred_date')->nullable();
                }
                if (!Schema::hasColumn('consultations', 'preferred_time_slot')) {
                    $table->string('preferred_time_slot')->default('morning');
                }
                if (!Schema::hasColumn('consultations', 'curtain_types')) {
                    $table->json('curtain_types')->nullable();
                }
                if (!Schema::hasColumn('consultations', 'estimated_windows')) {
                    $table->string('estimated_windows')->nullable();
                }
                if (!Schema::hasColumn('consultations', 'notes')) {
                    $table->text('notes')->nullable();
                }
                if (!Schema::hasColumn('consultations', 'assigned_staff_id')) {
                    $table->foreignId('assigned_staff_id')->nullable()->constrained('users')->nullOnDelete();
                }
                if (!Schema::hasColumn('consultations', 'admin_notes')) {
                    $table->text('admin_notes')->nullable();
                }
                if (!Schema::hasColumn('consultations', 'quoted_amount')) {
                    $table->decimal('quoted_amount', 15, 2)->nullable();
                }
            });
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('Migration update_consultations_table_structure: ' . $e->getMessage());
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Keep columns safely
    }
};
