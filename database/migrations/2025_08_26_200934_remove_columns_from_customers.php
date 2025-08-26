<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('customers', function (Blueprint $table) {
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
            'shipping_phone'
            ]);
        });
    }

    public function down()
    {
        Schema::table('customers', function (Blueprint $table) {
            // Optionally re-add columns if you want to be able to roll back
            // $table->string('column1')->nullable();
            // $table->integer('column2')->nullable();
        });
    }
};
