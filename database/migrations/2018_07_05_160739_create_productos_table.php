<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('productos', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('id_categoria');
            $table->string('codigo');
            $table->string('nombre');
            $table->longText('descripcion')->nullable();
            $table->mediumText('imagen');
            $table->integer('stock');
            $table->double('precio_compra');
            $table->double('precio_venta');
            $table->unsignedBigInteger('ventas')->nullable();
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
        Schema::dropIfExists('productos');
    }
}
