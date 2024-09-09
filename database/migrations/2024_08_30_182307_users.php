<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
// use App\Enums\UserRole; 

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('users')) { Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('nom')->nullable(false); 
            $table->string('prenom')->nullable(false); 
            $table->string('login')->unique()->nullable(false); 
            $table->string('password')->nullable(false); 
            $table->foreignId('role_id')->constrained('roles')->onDelete('set null');
            $table->timestamps();
        });
    }
    }
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};