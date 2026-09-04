<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // GHN Integration fields
            $table->string('ghn_order_code', 20)->nullable()->after('order_code')->index();
            $table->string('ghn_status', 50)->nullable()->after('shipping_status');
            $table->string('ghn_status_name')->nullable()->after('ghn_status');

            // Shipping destination detail (GHN location IDs)
            $table->unsignedInteger('to_district_id')->nullable()->after('shipping_address');
            $table->string('to_ward_code', 20)->nullable()->after('to_district_id');
            $table->integer('total_weight')->default(600)->after('to_ward_code');
            $table->timestamp('expected_delivery_at')->nullable()->after('total_weight');

            // Cancellation tracking
            $table->string('cancel_reason')->nullable()->after('notes');
            $table->timestamp('cancelled_at')->nullable()->after('cancel_reason');

            // Payment timestamp
            $table->timestamp('paid_at')->nullable()->after('cancelled_at');
        });

        // Expand shipping_status enum to include 'returning'
        DB::statement("ALTER TABLE orders MODIFY COLUMN shipping_status ENUM('pending','processing','shipping','delivered','returning','cancelled') DEFAULT 'pending'");

        // Expand payment_method enum to include 'momo'
        DB::statement("ALTER TABLE orders MODIFY COLUMN payment_method ENUM('cod','bank_transfer','momo') DEFAULT 'cod'");

        // Expand payment_status enum to include 'refunding'
        DB::statement("ALTER TABLE orders MODIFY COLUMN payment_status ENUM('pending','paid','failed','refunding') DEFAULT 'pending'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropIndex(['ghn_order_code']);
            $table->dropColumn([
                'ghn_order_code',
                'ghn_status',
                'ghn_status_name',
                'to_district_id',
                'to_ward_code',
                'total_weight',
                'expected_delivery_at',
                'cancel_reason',
                'cancelled_at',
                'paid_at',
            ]);
        });

        // Revert enums
        DB::statement("ALTER TABLE orders MODIFY COLUMN shipping_status ENUM('pending','processing','shipping','delivered','cancelled') DEFAULT 'pending'");
        DB::statement("ALTER TABLE orders MODIFY COLUMN payment_method ENUM('cod','bank_transfer') DEFAULT 'cod'");
        DB::statement("ALTER TABLE orders MODIFY COLUMN payment_status ENUM('pending','paid','failed') DEFAULT 'pending'");
    }
};
