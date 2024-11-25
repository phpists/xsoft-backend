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
            $table->bigInteger('company_id')->comment('Компанія до якої належить продукт');
            $table->bigInteger('user_id')->comment('Хто створив товар');
            $table->bigInteger('brand_id');
            $table->bigInteger('category_id');
            $table->string('article')->comment('Артикль');
            $table->string('title');
            $table->text('description')->nullable();
            $table->integer('product_measure_id')->comment('Одиниці виміру');
            $table->string('color')->nullable();
            $table->bigInteger('balance')->default(0)->comment('Критичний залишок');

            $table->bigInteger('materials_used_quantity')->nullable()->comment('Витратні матеріали кількість');
            $table->integer('materials_used_measure_id')->nullable()->comment('Витратні матеріали одиниця вимиру');

            $table->float('cost_price')->nullable()->comment('Собівартість');
            $table->float('retail_price')->nullable()->comment('Роздрібна ціна');
            $table->json('tags')->nullable();
            $table->json('vendors')->nullable();

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
