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
        Schema::table('orders', function (Blueprint $table) {
            $table->boolean('is_gift_wrapped')->default(false)->after('total_weight');
            $table->decimal('gift_wrap_fee', 12, 2)->default(0.00)->after('is_gift_wrapped');
            $table->text('gift_message')->nullable()->after('gift_wrap_fee');
            $table->boolean('hide_price')->default(false)->after('gift_message');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['is_gift_wrapped', 'gift_wrap_fee', 'gift_message', 'hide_price']);
        });
    }
};
