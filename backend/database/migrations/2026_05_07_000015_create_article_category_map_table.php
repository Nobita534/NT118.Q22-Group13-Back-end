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
        Schema::create('Article_Category_Map', function (Blueprint $table) {
            $table->unsignedInteger('Category_ID');
            $table->unsignedInteger('Article_ID');
            $table->boolean('Is_Primary')->default(false);

            $table->primary(['Category_ID', 'Article_ID']);
            $table->foreign('Category_ID')
                ->references('Category_ID')
                ->on('Category')
                ->onUpdate('restrict')
                ->onDelete('restrict');
            $table->foreign('Article_ID')
                ->references('Article_ID')
                ->on('Article')
                ->onUpdate('restrict')
                ->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('Article_Category_Map');
    }
};
