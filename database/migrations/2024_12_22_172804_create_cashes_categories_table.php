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
        Schema::create('cashes_categories', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('cashes_id'); // id каси
            $table->bigInteger('cash_category_id'); // id статей
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cashes_categories');
    }
};
