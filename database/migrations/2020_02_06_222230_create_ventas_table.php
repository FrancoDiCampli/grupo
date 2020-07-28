<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVentasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ventas', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('tipocomprobante');
            $table->bigInteger('numventa');
            $table->string('comprobanteadherido')->nullable();
            $table->bigInteger('cuit'); //cliente
            $table->string('fecha');
            $table->text('observaciones')->nullable();
            $table->decimal('bonificacion', 12, 2);
            $table->decimal('recargo', 12, 2);
            $table->boolean('pagada');
            // $table->json('referencia')->nullable();
            $table->string('condicionventa');
            $table->decimal('subtotal', 12, 2);
            $table->decimal('total', 12, 2);
            $table->decimal('subtotalPesos', 12, 2)->nullable();
            $table->decimal('totalPesos', 12, 2)->nullable();
            $table->decimal('cotizacion', 12, 2)->nullable();
            $table->string('fechaCotizacion')->nullable();
            $table->bigInteger('numfactura')->nullable();
            $table->bigInteger('cliente_id');
            $table->bigInteger('user_id');
            $table->timestamps();
            $table->softDeletes();

            // $table->foreign('cliente_id')->references('id')->on('clientes');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ventas');
    }
}
