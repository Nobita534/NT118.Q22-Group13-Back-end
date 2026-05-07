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
        Schema::create('Comments', function (Blueprint $table) {
            $table->increments('Comment_ID');
            $table->text('Content');
            $table->dateTime('CreatedAt')->useCurrent();
            $table->unsignedInteger('Article_ID')->nullable();
            $table->unsignedInteger('User_ID')->nullable();

            $table->foreign('Article_ID')
                ->references('Article_ID')
                ->on('Article')
                ->onUpdate('restrict')
                ->onDelete('restrict');
            $table->foreign('User_ID')
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
        Schema::dropIfExists('Comments');
    }
};
