<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateArticuloCompraTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('articulo_compra', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('codarticulo');
            $table->string('articulo');
            $table->string('medida');
            $table->integer('cantidad');
            $table->integer('litros');
            $table->integer('cantidadlitros');
            $table->decimal('preciounitario', 12, 2);
            $table->decimal('subtotal', 12, 2);
            $table->integer('lote')->nullable();
            $table->unsignedBigInteger('articulo_id');
            $table->unsignedBigInteger('compra_id');
            $table->timestamps();

            $table->foreign('compra_id')->references('id')->on('compras');
            $table->foreign('articulo_id')->references('id')->on('articulos');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('articulo_compra');
    }
}
