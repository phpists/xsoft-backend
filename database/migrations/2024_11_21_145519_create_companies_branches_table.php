<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Філії компаній
     */
    public function up(): void
    {
        Schema::create('companies_branches', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('company_id');
            $table->string('title');
            $table->string('location');
            $table->json('phones');
            $table->string('latitude')->nullable();
            $table->string('longitude')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('companies_branches');
    }
};
