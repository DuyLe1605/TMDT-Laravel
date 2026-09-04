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
            $table->decimal('avg_rating', 2, 1)->default(5.0)->after('is_active')->comment('Điểm đánh giá trung bình 1.0 - 5.0');
            $table->unsignedInteger('reviews_count')->default(0)->after('avg_rating')->comment('Tổng số lượt đánh giá đã duyệt');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['avg_rating', 'reviews_count']);
        });
    }
};
