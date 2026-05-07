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
        Schema::create('Product', function (Blueprint $table) {
            $table->increments('Product_ID');
            $table->string('ProductName');
            $table->string('Country')->nullable();
            $table->unsignedInteger('Brand_ID')->nullable();

            $table->foreign('Brand_ID')
                ->references('Brand_ID')
                ->on('Brand')
                ->onUpdate('restrict')
                ->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('Product');
    }
};
