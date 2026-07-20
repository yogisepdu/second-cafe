<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        /*
         * Mengubah enum menjadi varchar agar metode
         * pembayaran lebih mudah dikembangkan.
         */
        DB::statement(
            'ALTER TABLE `payments`
            MODIFY `method` VARCHAR(30) NOT NULL'
        );

        Schema::table(
            'payments',
            function (Blueprint $table): void {
                $table
                    ->decimal(
                        'amount_received',
                        12,
                        2
                    )
                    ->nullable()
                    ->after('amount');

                $table
                    ->decimal(
                        'change_amount',
                        12,
                        2
                    )
                    ->nullable()
                    ->after('amount_received');
            }
        );
    }

    public function down(): void
    {
        /*
         * Cegah rollback apabila sudah terdapat
         * pembayaran kasir agar data tidak rusak.
         */
        $hasUnsupportedMethod = DB::table(
            'payments'
        )
            ->whereNotIn('method', [
                'qris',
                'transfer_bank',
            ])
            ->exists();

        if ($hasUnsupportedMethod) {
            throw new RuntimeException(
                'Migration tidak dapat di-rollback karena sudah terdapat pembayaran kasir.'
            );
        }

        Schema::table(
            'payments',
            function (Blueprint $table): void {
                $table->dropColumn([
                    'amount_received',
                    'change_amount',
                ]);
            }
        );

        DB::statement(
            "ALTER TABLE `payments`
            MODIFY `method`
            ENUM('qris', 'transfer_bank')
            NOT NULL"
        );
    }
};
