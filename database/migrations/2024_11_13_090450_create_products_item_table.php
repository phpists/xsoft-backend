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
        Schema::create('products_item', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('product_id');
            $table->integer('tax_id')->nullable()->comment('Податки');
            $table->float('cost_price')->nullable()->comment('Собівартість');
            $table->float('retail_price')->nullable()->comment('Роздрібна ціна');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products_item');
    }
};
