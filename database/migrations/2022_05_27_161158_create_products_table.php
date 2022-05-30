<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //SKU, nombre, cantidad, precio, descripción e imagen, siendo nombre, cantidad y precio campos obligatorios.
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->integer('sku');
            $table->string('name');
            $table->integer('quantity');
            $table->float('price', 8, 2);
            $table->string('description');
            $table->string('image');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('products');
    }
}
