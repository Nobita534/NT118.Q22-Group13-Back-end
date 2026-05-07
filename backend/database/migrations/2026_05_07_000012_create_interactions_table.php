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
        Schema::create('Interactions', function (Blueprint $table) {
            $table->increments('InteractionId');
            $table->string('Type')->nullable();
            $table->dateTime('Timestamp')->nullable();
            $table->unsignedInteger('Article_ID')->nullable();
            $table->unsignedInteger('UserID')->nullable();

            $table->foreign('Article_ID')
                ->references('Article_ID')
                ->on('Article')
                ->onUpdate('restrict')
                ->onDelete('restrict');
            $table->foreign('UserID')
                ->references('User_ID')
                ->on('User')
                ->onUpdate('restrict')
                ->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('Interactions');
    }
};
