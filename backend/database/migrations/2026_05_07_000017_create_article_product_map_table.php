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
        Schema::create('Article_Product_Map', function (Blueprint $table) {
            $table->unsignedInteger('Product_ID');
            $table->unsignedInteger('Article_ID');

            $table->primary(['Product_ID', 'Article_ID']);
            $table->foreign('Product_ID')
                ->references('Product_ID')
                ->on('Product')
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
        Schema::dropIfExists('Article_Product_Map');
    }
};
