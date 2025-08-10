<?php

// database/migrations/xxxx_xx_xx_xxxxxx_add_myparcel_columns_to_orders.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('orders', function (Blueprint $table) {
            $table->longText('myparcel_delivery_json')->nullable()->after('myparcel_package_type_id');
            $table->boolean('myparcel_is_pickup')->default(false)->after('myparcel_delivery_json');
            $table->string('myparcel_carrier', 50)->nullable()->after('myparcel_is_pickup');
            $table->string('myparcel_delivery_type', 50)->nullable()->after('myparcel_carrier');
        });
    }
    public function down(): void {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'myparcel_delivery_json',
                'myparcel_is_pickup',
                'myparcel_carrier',
                'myparcel_delivery_type',
            ]);
        });
    }
};

