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
        Schema::table('kullanicis', function (Blueprint $table) {
            $table->string('email_verification_token', 6)->nullable()->after('paythor_token');
            $table->timestamp('email_verification_token_expires_at')->nullable()->after('email_verification_token');
            $table->timestamp('email_verified_at')->nullable()->after('email_verification_token_expires_at');
            $table->boolean('email_verified')->default(false)->after('email_verified_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kullanicis', function (Blueprint $table) {
            $table->dropColumn(['email_verification_token', 'email_verification_token_expires_at', 'email_verified_at', 'email_verified']);
        });
    }
};
