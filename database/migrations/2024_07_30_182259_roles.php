<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('role')->nullable(false); 
            $table->timestamps();
        });

        // Ajoutez des rôles par défaut
        DB::table('roles')->insert([
            ['role' => 'admin'],
            ['role' => 'client'],
            ['role' => 'boutiquier'],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('roles');
    }
};
