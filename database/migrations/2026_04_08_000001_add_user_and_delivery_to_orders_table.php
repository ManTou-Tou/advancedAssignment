<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->nullable()->index()->after('session_id');

            $table->string('delivery_name')->nullable()->after('status');
            $table->string('delivery_phone', 40)->nullable()->after('delivery_name');
            $table->string('delivery_address_line1')->nullable()->after('delivery_phone');
            $table->string('delivery_address_line2')->nullable()->after('delivery_address_line1');
            $table->string('delivery_city', 100)->nullable()->after('delivery_address_line2');
            $table->string('delivery_state', 100)->nullable()->after('delivery_city');
            $table->string('delivery_postcode', 20)->nullable()->after('delivery_state');
            $table->string('delivery_country', 100)->nullable()->after('delivery_postcode');

            $table->string('delivery_status', 30)->default('pending')->index()->after('delivery_country');
            $table->string('tracking_number', 100)->nullable()->after('delivery_status');
            $table->timestamp('shipped_at')->nullable()->after('tracking_number');
            $table->timestamp('delivered_at')->nullable()->after('shipped_at');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'user_id',
                'delivery_name',
                'delivery_phone',
                'delivery_address_line1',
                'delivery_address_line2',
                'delivery_city',
                'delivery_state',
                'delivery_postcode',
                'delivery_country',
                'delivery_status',
                'tracking_number',
                'shipped_at',
                'delivered_at',
            ]);
        });
    }
};

