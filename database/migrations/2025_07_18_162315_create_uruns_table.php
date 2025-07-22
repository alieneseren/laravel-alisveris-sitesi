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
        Schema::create('uruns', function (Blueprint $table) {
            $table->id();
            $table->foreignId('magaza_id')->constrained('magazas')->onDelete('cascade');
            $table->string('urun_adi');
            $table->text('urun_aciklamasi')->nullable();
            $table->decimal('fiyat', 10, 2);
            $table->integer('stok')->default(0);
            $table->enum('durum', ['aktif', 'pasif'])->default('aktif');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('uruns');
    }
};
