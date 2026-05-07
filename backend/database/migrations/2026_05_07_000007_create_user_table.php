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
        Schema::create('User', function (Blueprint $table) {
            $table->increments('User_ID');
            $table->string('Username');
            $table->string('Email')->unique();
            $table->string('PasswordHash');
            $table->text('Bio')->nullable();
            $table->string('Avatar')->nullable();
            $table->string('Role')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('User');
    }
};
