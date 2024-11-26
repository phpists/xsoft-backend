<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Персони та звязок до філій
     */
    public function up(): void
    {
        Schema::create('users_branches', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id')->comment('Id співробітника');
            $table->bigInteger('branch_id')->comment('Id філії');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users_branches');
    }
};
