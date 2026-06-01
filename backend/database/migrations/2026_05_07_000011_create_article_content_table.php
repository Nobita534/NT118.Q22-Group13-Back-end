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
        Schema::create('Article_Content', function (Blueprint $table) {
            $table->unsignedInteger('Article_ID');
            $table->longText('ContentHTML')->nullable();
            $table->longText('CleanText')->nullable();
            $table->longText('Sum_content')->nullable();
            $table->string('sum_voice_link')->nullable();
            $table->primary('Article_ID');
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
        Schema::dropIfExists('Article_Content');
    }
};
