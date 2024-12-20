<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Статті в касах
     */
    public function up(): void
    {
        Schema::create('cash_categories', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('company_id');
            $table->string('title');
            $table->integer('type_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cash_categories');
    }
};
