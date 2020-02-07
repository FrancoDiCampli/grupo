<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFacturaVentaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('factura_venta', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('factura_id');
            $table->unsignedBigInteger('venta_id');
            $table->timestamps();

            $table->foreign('factura_id')->references('id')->on('facturas');
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
        Schema::dropIfExists('factura_venta');
    }
}
