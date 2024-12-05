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
        Schema::create('products_movement_item', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('product_movement_id')->comment('Id приходу');
            $table->bigInteger('product_id');
            $table->integer('type_id')->comment('Тип списання Прихід/Продажа/Списання');
            $table->float('qty')->comment('Кількість');
            $table->integer('measurement_id')->comment('Одиниці виміру');
            $table->float('cost_price')->nullable()->comment('Ціна закупки');
            $table->float('retail_price')->nullable()->comment('Роздрібна ціна');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products_movement_item');
    }
};
