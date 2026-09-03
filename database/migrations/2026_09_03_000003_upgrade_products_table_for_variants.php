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
        Schema::table('products', function (Blueprint $table) {
            $table->foreignId('brand_id')
                ->nullable()
                ->after('category_id')
                ->constrained('brands')
                ->nullOnDelete();
            $table->boolean('has_variants')->default(false)->after('sale_price');
            $table->string('sku', 100)->nullable()->unique()->after('slug');

            $table->index('brand_id');
            $table->index('has_variants');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropForeign(['brand_id']);
            $table->dropColumn(['brand_id', 'has_variants', 'sku']);
        });
    }
};
