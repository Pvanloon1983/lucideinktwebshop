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
        Schema::create('customers', function (Blueprint $table) {
          $table->id();
          // Billing address fields
          $table->string('billing_first_name');
          $table->string('billing_last_name');
          $table->string('billing_email');
          $table->string('billing_company')->nullable();
          $table->string('billing_street');
          $table->string('billing_house_number');
          $table->string('billing_house_number_addition')->nullable();
          $table->string('billing_postal_code');
          $table->string('billing_city');
          $table->string('billing_country');
          $table->string('billing_phone')->nullable();

          // Shipping address fields (optional, for alternate shipping)
          $table->string('shipping_first_name')->nullable();
          $table->string('shipping_last_name')->nullable();
          $table->string('shipping_company')->nullable();
          $table->string('shipping_street')->nullable();
          $table->string('shipping_house_number')->nullable();
          $table->string('shipping_house_number_addition')->nullable();
          $table->string('shipping_postal_code')->nullable();
          $table->string('shipping_city')->nullable();
          $table->string('shipping_country')->nullable();
          $table->string('shipping_phone')->nullable();

          $table->softDeletes();
          $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
