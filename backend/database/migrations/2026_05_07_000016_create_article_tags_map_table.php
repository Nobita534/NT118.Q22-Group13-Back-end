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
        Schema::create('Article_Tags_Map', function (Blueprint $table) {
            $table->unsignedInteger('Tag_ID');
            $table->unsignedInteger('Article_ID');

            $table->primary(['Tag_ID', 'Article_ID']);
            $table->foreign('Tag_ID')
                ->references('Tag_ID')
                ->on('Tags')
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
        Schema::dropIfExists('Article_Tags_Map');
    }
};
