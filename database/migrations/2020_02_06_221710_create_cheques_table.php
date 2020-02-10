<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateChequesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cheques', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('numero');
            $table->string('fecharecibido');
            $table->string('fechacobro');
            $table->string('banco');
            $table->bigInteger('cuit');
            $table->string('emisor');
            $table->decimal('pesos', 12, 2);
            $table->decimal('dolares', 12, 2);
            $table->string('estado');
            $table->string('fecha_cotizacion');
            $table->decimal('cotizacion', 12, 2);
            $table->text('observaciones')->nullable();
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
        Schema::dropIfExists('cheques');
    }
}
