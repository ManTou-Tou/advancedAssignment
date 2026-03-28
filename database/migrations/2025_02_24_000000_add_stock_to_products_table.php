<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Skip if `stock` already exists (e.g. table created by 2025_02_22_000000_create_products_table).
        if (!Schema::hasTable('products') || Schema::hasColumn('products', 'stock')) {
            return;
        }

        Schema::table('products', function (Blueprint $table) {
            $table->unsignedInteger('stock')->default(0);
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('products') || !Schema::hasColumn('products', 'stock')) {
            return;
        }

        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('stock');
        });
    }
};
