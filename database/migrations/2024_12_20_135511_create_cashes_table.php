<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Каси
     */
    public function up(): void
    {
        Schema::create('cashes', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('company_id');
            $table->string('title');
            $table->string('appointment');
            $table->text('description');
            $table->boolean('is_cash_category'); // Прив’язати касу до статей
            $table->float('total')->default(0);
            $table->float('debt')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cashes');
    }
};
