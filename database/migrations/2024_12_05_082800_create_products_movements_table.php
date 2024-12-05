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
        Schema::create('products_movement', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('company_id')->comment('Id компанії');
            $table->bigInteger('staff_id')->comment('Особа яка прийняла');
            $table->bigInteger('warehouse_id')->comment('Склад товарів');
            $table->bigInteger('supplier_id')->comment('Постачальник');
            $table->bigInteger('box_office_id')->nullable()->comment('Каса');
            $table->integer('type_id')->comment('Тип списання Прихід/Продажа/Списання');
            $table->float('total_price')->default(0)->comment('Ціна закупки');
            $table->dateTime('date_create')->comment('Дата закупки');
            $table->time('time_create')->comment('Час закупки');
            $table->boolean('debt')->default(0)->comment('Дозволити борг');
            $table->boolean('installment_payment')->default(0)->comment('Сплата частково');
            $table->dateTime('box_office_date')->nullable()->comment('Дата каси');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products_movement');
    }
};
