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
        Schema::table('dettes', function (Blueprint $table) {
            $table->unsignedBigInteger('client_id')->default(1);; // Ajouter la colonne client_id

            // Définir client_id comme clé étrangère
            $table->foreign('client_id')->references('id')->on('clients')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('dettes', function (Blueprint $table) {
            // Supprimer la clé étrangère et la colonne
            $table->dropForeign(['client_id']);
            $table->dropColumn('client_id');
        });
    }
};
