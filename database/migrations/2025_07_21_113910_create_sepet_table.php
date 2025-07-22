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
        Schema::create('sepet', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('kullanici_id');
            $table->unsignedBigInteger('urun_id');
            $table->integer('miktar')->default(1);
            $table->timestamps();
            
            // Foreign key constraints
            $table->foreign('kullanici_id')->references('id')->on('kullanicis')->onDelete('cascade');
            $table->foreign('urun_id')->references('id')->on('uruns')->onDelete('cascade');
            
            // Unique constraint - bir kullanıcı aynı ürünü birden fazla kez ekleyemez
            $table->unique(['kullanici_id', 'urun_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sepet');
    }
};
