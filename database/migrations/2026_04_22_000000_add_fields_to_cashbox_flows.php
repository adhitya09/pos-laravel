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
        Schema::table('cashbox_flows', function (Blueprint $table) {
            $table->string('reference_type')->nullable()->after('notes');
            $table->unsignedBigInteger('reference_id')->nullable()->after('reference_type');
            $table->boolean('is_auto')->default(false)->after('reference_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cashbox_flows', function (Blueprint $table) {
            $table->dropColumn(['reference_type', 'reference_id', 'is_auto']);
        });
    }
};
