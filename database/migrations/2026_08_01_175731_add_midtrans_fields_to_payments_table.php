<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table(
            'payments',
            function (Blueprint $table): void {
                $table->string(
                    'gateway',
                    50,
                )
                    ->nullable()
                    ->after('method');

                $table->string(
                    'gateway_order_id',
                    100,
                )
                    ->nullable()
                    ->unique()
                    ->after('gateway');

                $table->string(
                    'gateway_transaction_id',
                    100,
                )
                    ->nullable()
                    ->index()
                    ->after('gateway_order_id');

                $table->text(
                    'qr_code_url',
                )->nullable();

                $table->longText(
                    'qr_string',
                )->nullable();

                $table->timestamp(
                    'expires_at',
                )
                    ->nullable()
                    ->index();

                $table->json(
                    'gateway_payload',
                )->nullable();
            },
        );
    }

    public function down(): void
    {
        Schema::table(
            'payments',
            function (Blueprint $table): void {
                $table->dropUnique([
                    'gateway_order_id',
                ]);

                $table->dropIndex([
                    'gateway_transaction_id',
                ]);

                $table->dropIndex([
                    'expires_at',
                ]);

                $table->dropColumn([
                    'gateway',
                    'gateway_order_id',
                    'gateway_transaction_id',
                    'qr_code_url',
                    'qr_string',
                    'expires_at',
                    'gateway_payload',
                ]);
            },
        );
    }
};
