<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoveMontantColumnsFromDettesTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('dettes', function (Blueprint $table) {
            $table->dropColumn(['montant_du', 'montant_restant']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('dettes', function (Blueprint $table) {
            $table->decimal('montant_du', 10, 2)->nullable();
            $table->decimal('montant_restant', 10, 2)->nullable();
        });
    }
}
