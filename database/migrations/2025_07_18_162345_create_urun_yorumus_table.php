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
        Schema::create('urun_yorumus', function (Blueprint $table) {
            $table->id();
            $table->foreignId('urun_id')->constrained('uruns')->onDelete('cascade');
            $table->foreignId('kullanici_id')->constrained('kullanicis')->onDelete('cascade');
            $table->integer('puan')->default(5);
            $table->text('yorum')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('urun_yorumus');
    }
};
