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
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_no')->unique();
            $table->date('invoice_date');
            $table->foreignId('customer_id')->constrained()->cascadeOnDelete();
            $table->string('customer_name')->nullable();
            $table->string('currency', 3)->default('USD');
            $table->decimal('total_amount', 12, 2)->default(0);
            $table->string('amount_in_words')->nullable();
            $table->timestamps();
        });

        Schema::create('invoice_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->constrained()->cascadeOnDelete();
            $table->string('item_description');
            $table->string('style_name')->nullable();
            $table->string('challan_no')->nullable();
            $table->integer('qty');
            $table->string('unit')->default('PCS');
            $table->decimal('rate', 18, 9);
            $table->decimal('amount', 12, 2);
            $table->timestamps();
        });

        // Pivot: which challans were included in an invoice
        Schema::create('invoice_challan', function (Blueprint $table) {
            $table->foreignId('invoice_id')->constrained()->cascadeOnDelete();
            $table->foreignId('challan_id')->constrained()->cascadeOnDelete();
            $table->primary(['invoice_id', 'challan_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoice_challan');
        Schema::dropIfExists('invoice_items');
        Schema::dropIfExists('invoices');
    }
};
