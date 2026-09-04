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
        Schema::create('vouchers', function (Blueprint $table) {
            $table->id();
            $table->string('code', 30)->unique()->index();
            $table->string('name', 150);
            $table->text('description')->nullable();
            
            // Discount configuration
            // percentage: discount X% with max_discount_amount cap
            // fixed_amount: discount X VND
            // shipping_discount: discount X VND on shipping fee
            $table->enum('discount_type', ['percentage', 'fixed_amount', 'shipping_discount'])->default('percentage');
            $table->decimal('discount_value', 14, 2); // % or VND
            $table->decimal('max_discount_amount', 14, 2)->nullable(); // Max cap for percentage or shipping
            $table->decimal('min_order_amount', 14, 2)->default(0); // Min subtotal required
            
            // Payment method restrictions: 'all', 'cod', 'bank_transfer', 'momo' or comma-separated
            $table->string('applicable_payment_methods', 100)->default('all');
            
            // Usage limits
            $table->unsignedInteger('usage_limit')->nullable(); // Total system-wide limit (null = unlimited)
            $table->unsignedInteger('used_count')->default(0); // Total times used
            $table->unsignedInteger('usage_limit_per_user')->default(1); // Per customer limit
            
            // Time validity
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            
            // Status toggle
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
        });

        Schema::create('voucher_usages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('voucher_id')->constrained('vouchers')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
            $table->decimal('discount_amount', 14, 2);
            $table->timestamp('used_at');

            $table->index(['voucher_id', 'user_id']);
            $table->index('order_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('voucher_usages');
        Schema::dropIfExists('vouchers');
    }
};
