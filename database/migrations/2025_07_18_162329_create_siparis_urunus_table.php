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
        Schema::create('siparis_urunus', function (Blueprint $table) {
            $table->id();
            $table->foreignId('siparis_id')->constrained('siparis')->onDelete('cascade');
            $table->foreignId('urun_id')->constrained('uruns')->onDelete('cascade');
            $table->integer('adet');
            $table->decimal('birim_fiyat', 10, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('siparis_urunus');
    }
};
