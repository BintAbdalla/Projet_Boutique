<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
// use App\Enums\UserRole;  // Assuming UserRole is an enum class in your app

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('clients', function (Blueprint $table) {
            $table->id(); 
            $table->string('surname'); 
            $table->string('adresse'); 
            $table->string('telephone')->unique(); 
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null'); 
            $table->timestamps(); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clients');
    }
};
