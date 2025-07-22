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
        Schema::create('urun_gorselis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('urun_id')->constrained('uruns')->onDelete('cascade');
            $table->string('gorsel_url');
            $table->boolean('ana_gorsel')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('urun_gorselis');
    }
};
