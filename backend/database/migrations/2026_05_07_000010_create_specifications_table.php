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
        Schema::create('Specifications', function (Blueprint $table) {
            $table->increments('Spec_ID');
            $table->string('SpecName');
            $table->text('SpecValue')->nullable();
            $table->unsignedInteger('Product_ID')->nullable();

            $table->foreign('Product_ID')
                ->references('Product_ID')
                ->on('Product')
                ->onUpdate('restrict')
                ->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('Specifications');
    }
};
