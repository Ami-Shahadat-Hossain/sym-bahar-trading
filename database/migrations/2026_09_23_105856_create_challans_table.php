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
        Schema::create('challans', function (Blueprint $table) {
            $table->id();
            $table->string('challan_no')->unique();
            $table->date('challan_date');
            $table->foreignId('customer_id')->constrained()->cascadeOnDelete();
            $table->string('buyer_name')->nullable();
            $table->string('currency', 3)->default('USD');
            $table->decimal('total_amount', 12, 2)->default(0);
            $table->string('amount_in_words')->nullable();
            $table->timestamps();
        });

        Schema::create('challan_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('challan_id')->constrained()->cascadeOnDelete();
            $table->string('item_description');
            $table->string('style_name')->nullable();
            $table->integer('qty');
            $table->string('unit')->default('PCS');
            $table->decimal('rate', 18, 9);
            $table->decimal('amount', 15, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('challan_items');
        Schema::dropIfExists('challans');
    }
};
