<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::table('products', function (Blueprint $table) {
            $table->string('base_title')->nullable()->after('title');
            $table->string('base_slug')->nullable()->after('slug');
        });
    }
    public function down() {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['base_title', 'base_slug']);
        });
    }
};
