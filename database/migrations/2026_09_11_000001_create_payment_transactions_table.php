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
        Schema::create('payment_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();

            // Payment gateway identification
            $table->string('gateway', 20);                // 'momo', 'vnpay', 'zalopay'...
            $table->string('transaction_type', 30)->default('payment'); // 'payment', 'refund', 'query'

            // MoMo-specific fields (nullable for other gateways)
            $table->string('partner_code', 50)->nullable();
            $table->string('request_id', 100)->unique();     // Idempotency key
            $table->string('momo_order_id', 200)->nullable(); // orderId sent to MoMo (differs from order_code)
            $table->bigInteger('amount');                   // Amount in VND (integer, no decimals)

            // Transaction status
            $table->string('status', 30)->default('pending'); // pending, paid, failed, refunded
            $table->integer('result_code')->nullable();       // MoMo resultCode (0 = success)
            $table->string('message', 500)->nullable();       // MoMo response message

            // MoMo response data
            $table->string('momo_trans_id', 100)->nullable(); // transId from MoMo
            $table->string('pay_type', 30)->nullable();       // qr, webApp, creditcard, napas
            $table->string('pay_url', 1000)->nullable();      // URL redirect to MoMo payment page
            $table->string('deeplink', 1000)->nullable();     // Deeplink to MoMo app
            $table->string('qr_code_url', 1000)->nullable();  // QR code URL

            // Signature verification audit trail
            $table->text('signature_request')->nullable();    // Signature sent to MoMo
            $table->text('signature_response')->nullable();   // Signature received from MoMo

            // Full raw data for auditing & debugging
            $table->json('request_payload')->nullable();      // Full request JSON sent to MoMo
            $table->json('response_payload')->nullable();     // Full response JSON from MoMo
            $table->json('ipn_payload')->nullable();          // Full IPN callback JSON from MoMo

            // Timestamps
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();

            // Indexes for efficient querying
            $table->index('order_id');
            $table->index('gateway');
            $table->index('status');
            $table->index('momo_trans_id');
            $table->index('momo_order_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_transactions');
    }
};
