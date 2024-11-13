<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Товари
     */
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id');
            $table->bigInteger('brand_id');
            $table->bigInteger('category_id');
            $table->string('article')->comment('Артикль');
            $table->string('title');
            $table->text('description')->nullable();
            $table->integer('product_measure_id')->comment('Одиниці виміру');
            $table->string('color')->nullable();
            $table->bigInteger('balance')->default(0)->comment('Критичний залишок');

            $table->bigInteger('materials_used_quantity')->comment('Витратні матеріали кількість');
            $table->integer('materials_used_measure_id')->comment('Витратні матеріали одиниця вимиру');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
