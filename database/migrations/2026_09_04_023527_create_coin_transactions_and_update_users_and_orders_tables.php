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
        // Thêm số dư xu vào bảng users
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedInteger('coins_balance')->default(0)->after('remember_token');
        });

        // Thêm thông tin xu đã dùng vào bảng orders
        Schema::table('orders', function (Blueprint $table) {
            $table->unsignedInteger('coins_used')->default(0)->after('discount_amount');
            $table->decimal('coins_discount_amount', 12, 2)->default(0)->after('coins_used');
        });

        // Bảng nhật ký biến động xu (Ledger)
        Schema::create('coin_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->enum('type', ['earn', 'spend', 'refund', 'adjust'])->comment('earn: nhận xu, spend: tiêu xu, refund: hoàn xu, adjust: điều chỉnh');
            $table->integer('amount')->comment('Số xu biến động (dương khi nhận/hoàn, âm khi tiêu)');
            $table->unsignedInteger('balance_after')->comment('Số dư ví sau giao dịch');
            $table->string('reference_type', 100)->nullable()->comment('Loại đối tượng liên kết (review, order, etc.)');
            $table->unsignedBigInteger('reference_id')->nullable()->comment('ID đối tượng liên kết');
            $table->string('description', 255)->comment('Nội dung mô tả giao dịch');
            $table->timestamps();

            $table->index(['user_id', 'created_at']);
            $table->index(['reference_type', 'reference_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('coin_transactions');

        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['coins_used', 'coins_discount_amount']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('coins_balance');
        });
    }
};
