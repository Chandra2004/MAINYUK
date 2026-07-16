<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('courts', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('sport_type', ['futsal', 'bulutangkis', 'basket', 'tenis', 'padel', 'voli']);
            $table->string('location');       // city slug: jakarta-selatan
            $table->string('city');           // display: Jakarta Selatan
            $table->string('address');
            $table->unsignedInteger('price_per_hour');
            $table->decimal('rating', 3, 1)->default(0.0);
            $table->unsignedInteger('total_reviews')->default(0);
            $table->text('description')->nullable();
            $table->json('facilities')->nullable();  // ["WiFi","Parkir","AC","Shower"]
            $table->json('images')->nullable();       // ["url1","url2",...]
            $table->json('courts_detail')->nullable();// [{"id":"L1","name":"Lapangan 1","price":50000}]
            $table->time('open_time')->default('07:00:00');
            $table->time('close_time')->default('22:00:00');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // ─── Testimonials / Ulasan Lapangan ──────────────────────
        Schema::create('testimonials', function (Blueprint $table) {
            $table->id();
            $table->foreignId('court_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('avatar')->nullable();       // URL foto reviewer (dari user.avatar atau input manual)
            $table->string('name');                     // Nama reviewer
            $table->text('comment');                    // Isi ulasan
            $table->unsignedTinyInteger('rating');      // 1 – 5 bintang
            $table->timestamps();

            $table->index('court_id');
            $table->index(['court_id', 'rating']);
        });

        // ─── Peralatan Sewa per Lapangan ─────────────────────────
        Schema::create('equipment', function (Blueprint $table) {
            $table->id();
            $table->foreignId('court_id')->constrained()->onDelete('cascade');
            $table->string('name');                     // "Raket", "Bola", "Sepatu"
            $table->string('icon')->default('fa-solid fa-box'); // FontAwesome class
            $table->string('description')->nullable();  // "Yonex Astrox, kondisi baik"
            $table->unsignedInteger('price_per_unit');  // Harga sewa per 1 alat
            $table->unsignedTinyInteger('stock')->default(10); // Stok tersedia
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index('court_id');
        });

        // ─── Kode Promo ───────────────────────────────────────────
        Schema::create('promos', function (Blueprint $table) {
            $table->id();
            $table->string('code', 30)->unique();       // "MAINYUK20"
            $table->string('description');              // Keterangan promo
            $table->unsignedTinyInteger('discount_percent'); // 10, 15, 20
            $table->foreignId('court_id')->nullable()->constrained()->nullOnDelete(); // NULL = berlaku semua lapangan
            $table->boolean('is_active')->default(true);
            $table->date('valid_until')->nullable();    // Null = tidak ada batas waktu
            $table->timestamps();

            $table->index('code');
            $table->index('court_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('promos');
        Schema::dropIfExists('equipment');
        Schema::dropIfExists('testimonials');
        Schema::dropIfExists('courts');
    }
};
