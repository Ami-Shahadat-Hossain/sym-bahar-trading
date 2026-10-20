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
        Schema::create('money_receipts', function (Blueprint $table) {
            $table->id();
            $table->string('receipt_no')->unique();
            $table->date('receipt_date');
            $table->foreignId('invoice_id')->constrained()->cascadeOnDelete();
            $table->string('received_from');
            $table->decimal('amount', 12, 2);
            $table->string('currency', 3)->default('USD'); // ← new
            $table->string('amount_in_words');
            $table->string('against_bill_no')->nullable();
            $table->string('mode')->default('Cash'); // Cash, Cheque, Bank Transfer, Bkash, Nagad, Bangla QR
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('money_receipts');
    }
};
