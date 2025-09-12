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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug');
            $table->unique(['title', 'deleted_at']);
            $table->unique(['slug', 'deleted_at']);
            $table->foreignId('category_id')->nullable()->constrained('product_categories')->nullOnDelete();
            $table->text('short_description')->nullable();
            $table->text('long_description')->nullable();
            $table->decimal('price', 8, 2)->nullable();
            $table->integer('stock')->default(0)->nullable();
            $table->foreignId('product_copy_id')->nullable()->constrained('product_copies')->nullOnDelete();
            $table->decimal('weight', 8, 0)->nullable();
            $table->decimal('height', 8, 0)->nullable();
            $table->decimal('width', 8, 0)->nullable();
            $table->decimal('depth', 8, 0)->nullable();
            $table->string('image_1')->nullable();
            $table->string('image_2')->nullable();
            $table->string('image_3')->nullable();
            $table->string('image_4')->nullable();
            $table->boolean('is_published')->default('0');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('deleted_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
