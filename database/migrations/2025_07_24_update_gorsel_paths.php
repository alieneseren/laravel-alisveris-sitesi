<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Eski görsel yollarını yeni yola güncelle
        DB::table('urun_gorselis')
            ->where('gorsel_url', 'LIKE', 'storage/uploads/urunler/%')
            ->update([
                'gorsel_url' => DB::raw("REPLACE(gorsel_url, 'storage/uploads/urunler/', 'gorseller/')")
            ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Geri alma işlemi
        DB::table('urun_gorselis')
            ->where('gorsel_url', 'LIKE', 'gorseller/%')
            ->update([
                'gorsel_url' => DB::raw("REPLACE(gorsel_url, 'gorseller/', 'storage/uploads/urunler/')")
            ]);
    }
};
