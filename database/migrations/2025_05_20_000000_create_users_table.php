<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();

            // Campi personalizzati
            $table->string('name');
            $table->string('surname');
            $table->string('email')->unique();
            $table->string('password');

            // Laravel default
            $table->rememberToken(); // Token per "ricordami"
            $table->timestamps();    // created_at e updated_at
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
