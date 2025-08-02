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
        Schema::create('order_item', function (Blueprint $table) {
          $table->id();
          $table->foreignId('order_id')->constrained()->cascadeOnDelete();
          $table->unsignedBigInteger('product_id');
          $table->string('product_name');
          $table->integer('quantity');
          $table->decimal('unit_price', 10, 2); // Store price at time of order
          $table->decimal('subtotal', 10, 2);
          $table->softDeletes();
          $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
