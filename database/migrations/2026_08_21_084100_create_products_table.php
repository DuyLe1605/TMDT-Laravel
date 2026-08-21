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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')
                ->constrained('categories')
                ->onDelete('cascade');
            $table->string('name', 255);
            $table->string('slug', 255)->unique();
            $table->decimal('price', 12, 2)->unsigned();
            $table->decimal('sale_price', 12, 2)->unsigned()->nullable();
            $table->unsignedInteger('stock')->default(0);
            $table->string('material', 255)->nullable()->comment('Chất liệu: Da bò, Da PU, Tweed, Canvas...');
            $table->string('dimensions', 100)->nullable()->comment('Kích thước: 22 x 8 x 15 cm...');
            $table->string('color', 100)->nullable()->comment('Màu sắc: Đen, Trắng kem, Nâu caramel...');
            $table->text('description')->nullable();
            $table->string('image', 500)->nullable();
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            // Indexes for fast querying & filtering
            $table->index('category_id');
            $table->index('price');
            $table->index('is_active');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
