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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cafe_table_id')
                ->constrained()
                ->restrictOnDelete();

            $table->string('order_code')->unique();
            $table->string('customer_name')->nullable();

            $table->enum('status', [
                'menunggu_pembayaran',
                'menunggu_verifikasi',
                'diterima',
                'diproses',
                'siap',
                'selesai',
                'dibatalkan',
            ])->default('menunggu_pembayaran');

            $table->decimal('subtotal', 12, 2)->default(0);
            $table->decimal('total_amount', 12, 2)->default(0);
            $table->text('notes')->nullable();
            $table->timestamp('ordered_at')->nullable();
            $table->timestamps();

            $table->index(['status', 'ordered_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
