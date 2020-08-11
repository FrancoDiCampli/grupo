<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateArticuloEntregaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('articulo_entrega', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('codarticulo');
            $table->string('articulo');
            $table->string('medida');
            $table->integer('cantidad');
            $table->integer('cantidadLitros');
            $table->unsignedBigInteger('articulo_id');
            $table->unsignedBigInteger('entrega_id');
            $table->unsignedBigInteger('articulo_venta_id');
            $table->timestamps();

            $table->foreign('articulo_id')->references('id')->on('articulos');
            $table->foreign('entrega_id')->references('id')->on('entregas');
            $table->foreign('articulo_venta_id')->references('id')->on('articulo_venta');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('articulo_entrega');
    }
}
