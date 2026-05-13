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
        Schema::table('payment_methods', function (Blueprint $table) {
            $table->string('logo')->nullable()->after('name');
            $table->boolean('is_cash')->default(true)->after('is_active')->comment('True = Cash/Direct, False = Non-Cash (QRIS, Transfer)');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payment_methods', function (Blueprint $table) {
            $table->dropColumn(['logo', 'is_cash']);
        });
    }
};
