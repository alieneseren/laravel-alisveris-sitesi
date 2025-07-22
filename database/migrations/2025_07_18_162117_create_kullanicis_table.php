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
        Schema::create('kullanicis', function (Blueprint $table) {
            $table->id();
            $table->string('ad');
            $table->string('eposta')->unique();
            $table->string('sifre');
            $table->enum('rol', ['yonetici', 'satici', 'musteri'])->default('musteri');
            $table->string('paythor_token')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kullanicis');
    }
};
