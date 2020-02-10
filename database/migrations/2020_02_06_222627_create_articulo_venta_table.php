<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateArticuloVentaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('articulo_venta', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('codarticulo');
            $table->string('articulo');
            $table->string('medida');
            $table->integer('cantidad');
            $table->integer('cantidadLitros');
            // $table->decimal('bonificacion', 12, 2);
            // $table->decimal('alicuota', 12, 2);
            $table->decimal('preciounitario', 12, 2);
            $table->decimal('subtotal', 12, 2);
            $table->decimal('subtotalPesos', 12, 2)->nullable();
            $table->decimal('cotizacion', 12, 2)->nullable();
            $table->string('fechaCotizacion')->nullable();
            $table->unsignedBigInteger('articulo_id');
            $table->unsignedBigInteger('venta_id');
            $table->timestamps();

            $table->foreign('articulo_id')->references('id')->on('articulos');
            $table->foreign('venta_id')->references('id')->on('ventas');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('articulo_venta');
    }
}
