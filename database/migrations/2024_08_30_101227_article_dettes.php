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
        Schema::create('article_dettes', function (Blueprint $table) {
            $table->id(); 
            $table->unsignedBigInteger('id_article'); 
            $table->unsignedBigInteger('id_dette');
            $table->integer('qteVente'); 
            $table->decimal('prixVente', 10, 2); 
            $table->timestamps();

            // Ajout des clés étrangères
            $table->foreign('id_article')->references('id')->on('articles')->onDelete('set null');
            $table->foreign('id_dette')->references('id')->on('dettes')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('article_dettes'); 
    }
};
