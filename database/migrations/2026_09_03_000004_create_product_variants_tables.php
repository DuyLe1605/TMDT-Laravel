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
        Schema::dropIfExists('product_variant_attribute_values');
        Schema::dropIfExists('product_variants');
        Schema::dropIfExists('product_attribute_values');
        Schema::dropIfExists('product_attributes');

        // 1. Nhóm thuộc tính sản phẩm (ví dụ: Chất liệu, Màu sắc, Kích cỡ)
        Schema::create('product_attributes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->string('name', 100);
            $table->unsignedSmallInteger('position')->default(0);
            $table->timestamps();

            $table->index(['product_id', 'position']);
        });

        // 2. Các giá trị của thuộc tính (ví dụ: Da thật Epsom, Cam Hermes)
        Schema::create('product_attribute_values', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_attribute_id')->constrained('product_attributes')->cascadeOnDelete();
            $table->string('value', 255);
            $table->string('color_code', 50)->nullable();
            $table->timestamps();

            $table->index('product_attribute_id');
        });

        // 3. Bảng Biến thể / SKU sản phẩm cụ thể
        Schema::create('product_variants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->string('sku', 100)->nullable()->unique();
            $table->string('variant_title', 255);
            $table->decimal('price', 14, 2)->unsigned();
            $table->decimal('sale_price', 14, 2)->unsigned()->nullable();
            $table->unsignedInteger('stock')->default(0);
            $table->string('image', 500)->nullable();
            $table->string('option1_value', 100)->nullable();
            $table->string('option2_value', 100)->nullable();
            $table->string('option3_value', 100)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['product_id', 'is_active']);
            $table->index(['product_id', 'option1_value', 'option2_value']);
        });

        // 4. Pivot giữa Product Variant và Attribute Value (dùng tên FK ngắn để không vượt quá 64 ký tự của MySQL)
        Schema::create('product_variant_attribute_values', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_variant_id');
            $table->unsignedBigInteger('product_attribute_value_id');
            $table->timestamps();

            $table->foreign('product_variant_id', 'fk_pvav_variant_id')
                ->references('id')
                ->on('product_variants')
                ->cascadeOnDelete();

            $table->foreign('product_attribute_value_id', 'fk_pvav_attr_val_id')
                ->references('id')
                ->on('product_attribute_values')
                ->cascadeOnDelete();

            $table->unique(['product_variant_id', 'product_attribute_value_id'], 'pvav_variant_attr_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_variant_attribute_values');
        Schema::dropIfExists('product_variants');
        Schema::dropIfExists('product_attribute_values');
        Schema::dropIfExists('product_attributes');
    }
};
