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
        Schema::table('orders', function (Blueprint $table) {
          $table->string('shipping_first_name')->nullable()->after('customer_id');
          $table->string('shipping_last_name')->nullable()->after('shipping_first_name');
          $table->string('shipping_company')->nullable()->after('shipping_last_name');
          $table->string('shipping_street')->nullable()->after('shipping_company');
          $table->string('shipping_house_number')->nullable()->after('shipping_street');
          $table->string('shipping_house_number_addition')->nullable()->after('shipping_house_number');
          $table->string('shipping_postal_code')->nullable()->after('shipping_house_number_addition');
          $table->string('shipping_city')->nullable()->after('shipping_postal_code');
          $table->string('shipping_country')->nullable()->after('shipping_city');
          $table->string('shipping_phone')->nullable()->after('shipping_country');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
            'shipping_first_name',
            'shipping_last_name',
            'shipping_company',
            'shipping_street',
            'shipping_house_number',
            'shipping_house_number_addition',
            'shipping_postal_code',
            'shipping_city',
            'shipping_country',
            'shipping_phone',
            ]);
        });
    }
};
