<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateArticuloGiveBackTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('articulo_give_back', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('codarticulo');
            $table->string('articulo');
            $table->string('medida');
            $table->string('litros');
            $table->integer('cantidad');
            $table->integer('cantidadLitros');
            $table->decimal('preciounitario', 12, 2);
            $table->decimal('subtotal', 12, 2);
            $table->decimal('subtotalPesos', 12, 2)->nullable();
            $table->decimal('cotizacion', 12, 2)->nullable();
            $table->string('fechaCotizacion')->nullable();
            $table->unsignedBigInteger('articulo_id');
            $table->unsignedBigInteger('give_back_id');
            $table->timestamps();

            $table->foreign('articulo_id')->references('id')->on('articulos');
            $table->foreign('give_back_id')->references('id')->on('give_backs');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('articulo_give_back');
    }
}
