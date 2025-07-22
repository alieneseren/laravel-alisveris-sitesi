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
        Schema::table('uruns', function (Blueprint $table) {
            $table->renameColumn('urun_adi', 'ad');
            $table->renameColumn('urun_aciklamasi', 'aciklama');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('uruns', function (Blueprint $table) {
            $table->renameColumn('ad', 'urun_adi');
            $table->renameColumn('aciklama', 'urun_aciklamasi');
        });
    }
};
