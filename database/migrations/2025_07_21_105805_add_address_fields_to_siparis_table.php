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
        Schema::table('siparis', function (Blueprint $table) {
            $table->text('teslimat_adresi')->nullable()->after('durum');
            $table->string('teslimat_telefonu')->nullable()->after('teslimat_adresi');
            $table->json('fatura_bilgileri')->nullable()->after('teslimat_telefonu');
            $table->string('payment_reference')->nullable()->after('fatura_bilgileri');
            $table->enum('payment_status', ['pending', 'paid', 'failed', 'cancelled'])->default('pending')->after('payment_reference');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('siparis', function (Blueprint $table) {
            $table->dropColumn(['teslimat_adresi', 'teslimat_telefonu', 'fatura_bilgileri', 'payment_reference', 'payment_status']);
        });
    }
};
