<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaiementsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('paiements', function (Blueprint $table) {
            $table->id(); // clé primaire auto-incrémentée
            $table->unsignedBigInteger('dette_id'); // Clé étrangère vers la table dettes
            $table->decimal('montant', 10, 2); // montant du paiement
            $table->timestamp('date_paiement')->useCurrent(); // date du paiement
            $table->timestamps(); // champs created_at et updated_at

            // Définir les relations de clé étrangère
            $table->foreign('dette_id')->references('id')->on('dettes')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('paiements');
    }
}