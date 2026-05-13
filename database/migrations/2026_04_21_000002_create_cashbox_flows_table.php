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
        Schema::create('cashbox_flows', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['in', 'out']);
            $table->foreignId('source_id')->constrained('cash_flow_sources')->onDelete('restrict');
            $table->unsignedBigInteger('amount');
            $table->date('date');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('date');
            $table->index('type');
            $table->index('source_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cashbox_flows');
    }
};
