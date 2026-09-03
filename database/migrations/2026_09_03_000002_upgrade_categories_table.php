<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->foreignId('parent_id')
                ->nullable()
                ->after('id')
                ->constrained('categories')
                ->nullOnDelete();
            $table->string('slug', 255)->nullable()->unique()->after('name');
            $table->text('description')->nullable()->after('slug');
            $table->string('image', 500)->nullable()->after('description');
            $table->boolean('is_active')->default(true)->after('image');

            $table->index('parent_id');
            $table->index('is_active');
        });

        // Generate slugs for existing categories if any
        $categories = DB::table('categories')->get();
        foreach ($categories as $category) {
            $slug = Str::slug($category->name);
            DB::table('categories')->where('id', $category->id)->update([
                'slug' => $slug,
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->dropForeign(['parent_id']);
            $table->dropColumn(['parent_id', 'slug', 'description', 'image', 'is_active']);
        });
    }
};
