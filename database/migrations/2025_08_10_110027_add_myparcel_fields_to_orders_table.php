<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up()
  {
    Schema::table('orders', function (Blueprint $table) {
      $table->string('myparcel_consignment_id')->nullable()->after('paid_at');
      $table->string('myparcel_track_trace_url')->nullable()->after('myparcel_consignment_id');
      $table->string('myparcel_label_link')->nullable()->after('myparcel_track_trace_url');
      $table->unsignedTinyInteger('myparcel_package_type_id')->nullable()->after('myparcel_label_link');
      $table->boolean('myparcel_only_recipient')->nullable()->after('myparcel_package_type_id');
      $table->boolean('myparcel_signature')->nullable()->after('myparcel_only_recipient');
      $table->integer('myparcel_insurance_amount')->nullable()->after('myparcel_signature');
    });
  }

  public function down()
  {
    Schema::table('orders', function (Blueprint $table) {
      $table->dropColumn([
        'myparcel_consignment_id',
        'myparcel_track_trace_url',
        'myparcel_label_link',
        'myparcel_package_type_id',
        'myparcel_only_recipient',
        'myparcel_signature',
        'myparcel_insurance_amount',
      ]);
    });
  }
};