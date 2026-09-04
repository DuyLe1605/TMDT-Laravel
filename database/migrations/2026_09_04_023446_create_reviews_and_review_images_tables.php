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
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('order_item_id')->unique()->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('rating')->comment('1-5 stars');
            $table->text('comment')->nullable();
            $table->string('product_variant_title', 150)->nullable()->comment('Snapshot phân loại sản phẩm lúc mua');
            $table->boolean('is_verified_purchase')->default(true);
            $table->unsignedInteger('coins_rewarded')->default(0)->comment('Số xu thưởng cho đánh giá');
            $table->boolean('is_visible')->default(true)->comment('Trạng thái hiển thị công khai');
            $table->text('admin_reply')->nullable()->comment('Phản hồi từ Aurelia Official Shop');
            $table->timestamp('admin_replied_at')->nullable();
            $table->timestamps();

            $table->index(['product_id', 'is_visible']);
            $table->index(['product_id', 'rating']);
            $table->index(['user_id']);
        });

        Schema::create('review_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('review_id')->constrained()->cascadeOnDelete();
            $table->string('image_path', 255);
            $table->unsignedTinyInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('review_images');
        Schema::dropIfExists('reviews');
    }
};
