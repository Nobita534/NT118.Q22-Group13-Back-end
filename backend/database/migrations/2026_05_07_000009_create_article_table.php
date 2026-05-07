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
        Schema::create('Article', function (Blueprint $table) {
            $table->increments('Article_ID');
            $table->string('Title');
            $table->string('Slug')->unique();
            $table->string('ThumbnailURL')->nullable();
            $table->string('Original_URL')->nullable();
            $table->string('URL_Hash')->nullable();
            $table->dateTime('PublishDate')->nullable();
            $table->integer('ViewCount')->default(0);
            $table->string('Status')->nullable();
            $table->unsignedInteger('Source_ID')->nullable();

            $table->foreign('Source_ID')
                ->references('Source_ID')
                ->on('Source')
                ->onUpdate('restrict')
                ->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('Article');
    }
};
