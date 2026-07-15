<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->string('booking_code', 20)->unique();  // MY-XXXXXX
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('court_id')->constrained()->onDelete('cascade');
            $table->date('date');
            $table->time('time_start');
            $table->time('time_end');
            $table->unsignedTinyInteger('duration_hours')->default(1);
            $table->string('court_detail')->nullable();    // "Lapangan 1"
            $table->unsignedInteger('subtotal');
            $table->unsignedInteger('discount')->default(0);
            $table->unsignedInteger('total_price');
            $table->unsignedInteger('paid_amount')->nullable();  // Nilai rupiah aktual yang dibayar (DP/Lunas)
            $table->enum('pay_type', ['lunas', 'dp50', 'dp30'])->default('lunas');
            $table->enum('status', ['pending', 'active', 'completed', 'cancelled'])->default('pending');
            $table->string('payment_status', 30)->nullable();    // Status pembayaran dari Midtrans (settlement, pending, dll)
            $table->string('payment_method')->nullable();        // "QRIS", "GoPay", etc
            $table->string('payment_channel')->nullable();
            $table->string('midtrans_order_id')->nullable();
            $table->text('midtrans_snap_token')->nullable();
            $table->string('midtrans_transaction_id', 50)->nullable(); // ID transaksi dari Midtrans
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('payment_expired_at')->nullable();  // Batas waktu pembayaran
            $table->json('items')->nullable();                  // slot details
            $table->timestamps();

            // Index untuk query yang sering digunakan
            $table->index(['user_id', 'status']);        // history + filter status
            $table->index(['court_id', 'date']);         // cek slot tersedia
            $table->index(['status', 'date']);           // admin query
            $table->index('midtrans_order_id');          // webhook lookup
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
