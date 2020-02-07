<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePagosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pagos', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('ctacte_id');
            $table->bigInteger('numpago');
            $table->decimal('importe', 12, 2);
            $table->string('fecha');
            $table->string('referencia');
            $table->timestamps();

            $table->foreign('ctacte_id')->references('id')->on('cuentacorrientes');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pagos');
    }
}
