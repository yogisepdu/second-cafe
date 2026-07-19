<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->uuid('public_token')
                ->nullable()
                ->unique()
                ->after('order_code');

            $table->string('customer_phone', 20)
                ->nullable()
                ->after('customer_name');

            $table->string('customer_email')
                ->nullable()
                ->after('customer_phone');

            $table->enum('payment_method', [
                'cashier',
                'online',
            ])
                ->nullable()
                ->after('customer_email');

            $table->enum('payment_status', [
                'unpaid',
                'pending',
                'paid',
                'failed',
                'cancelled',
            ])
                ->default('unpaid')
                ->after('payment_method');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropUnique([
                'public_token',
            ]);

            $table->dropColumn([
                'public_token',
                'customer_phone',
                'customer_email',
                'payment_method',
                'payment_status',
            ]);
        });
    }
};
