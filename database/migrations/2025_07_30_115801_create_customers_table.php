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
          $table->string('first_name');
          $table->string('last_name');
          $table->string('email');
          $table->string('company')->nullable();
          $table->string('street');
          $table->string('house_number');
          $table->string('house_number_addition')->nullable();
          $table->string('postal_code');
          $table->string('city');
          $table->string('country');
          $table->string('phone');
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
