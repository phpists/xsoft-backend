<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Історія тразакцій каси
     */
    public function up(): void
    {
        Schema::create('cashes_history', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id')->comment('Id користувача який здійснив операцію');
            $table->bigInteger('cashes_id')->comment(' Id каси');
            $table->integer('type_id')->comment('Тип списання Прихід/Продажа/Списання');
            $table->float('amount')->default(0)->comment('Сума');
            $table->float('amount_cashes')->default(0)->comment('Баланс каси');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cashes_history');
    }
};
