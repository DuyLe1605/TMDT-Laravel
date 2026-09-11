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
        Schema::create('gift_options', function (Blueprint $table) {
            $table->id();
            $table->string('type', 20)->comment('paper, card');
            $table->string('name', 100);
            $table->string('code', 50)->unique();
            $table->string('image', 255)->nullable()->comment('Sample photo or hex color/swatch');
            $table->decimal('price', 12, 2)->default(0.00);
            $table->string('description', 255)->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->index(['type', 'is_active']);
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->foreignId('gift_paper_id')->nullable()->after('is_gift_wrapped')->constrained('gift_options')->nullOnDelete();
            $table->string('gift_paper_name', 100)->nullable()->after('gift_paper_id');
            $table->foreignId('gift_card_id')->nullable()->after('gift_paper_name')->constrained('gift_options')->nullOnDelete();
            $table->string('gift_card_name', 100)->nullable()->after('gift_card_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['gift_paper_id']);
            $table->dropForeign(['gift_card_id']);
            $table->dropColumn(['gift_paper_id', 'gift_paper_name', 'gift_card_id', 'gift_card_name']);
        });

        Schema::dropIfExists('gift_options');
    }
};
