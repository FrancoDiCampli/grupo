<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePresupuestosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('presupuestos', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('ptoventa');
            $table->integer('numpresupuesto');
            $table->bigInteger('cuit');
            $table->string('fecha');
            $table->decimal('bonificacion', 12, 2);
            $table->decimal('recargo', 12, 2);
            $table->decimal('subtotal', 12, 2);
            $table->decimal('total', 12, 2);
            $table->decimal('subtotalPesos', 12, 2)->nullable();
            $table->decimal('totalPesos', 12, 2)->nullable();
            $table->decimal('cotizacion', 12, 2)->nullable();
            $table->string('fechaCotizacion')->nullable();
            $table->string('vencimiento')->nullable();
            $table->string('observaciones')->nullable();
            $table->UnsignedBigInteger('cliente_id');
            $table->bigInteger('user_id');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('cliente_id')->references('id')->on('clientes');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('presupuestos');
    }
}
