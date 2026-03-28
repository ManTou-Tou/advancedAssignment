<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Runs before cart_items / orders (which reference products).
     */
    public function up(): void
    {
        if (Schema::hasTable('products')) {
            return;
        }

        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('brand', 100);
            $table->enum('category', ['phones', 'laptops']);
            $table->decimal('price', 10, 2);
            $table->decimal('rating', 2, 1);
            $table->text('image');
            $table->unsignedInteger('stock')->default(0);
            $table->timestamps();

            $table->index('brand');
            $table->index('category');
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
