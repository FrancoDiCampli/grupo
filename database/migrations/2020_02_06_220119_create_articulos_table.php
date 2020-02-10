<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateArticulosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('articulos', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('codarticulo');
            $table->string('articulo');
            $table->text('descripcion');
            $table->string('medida')->default('Litros');
            $table->decimal('precio', 12, 2);
            $table->integer('stockminimo')->default(1);
            $table->unsignedBigInteger('marca_id');
            $table->unsignedBigInteger('categoria_id');
            $table->string('foto')->nullable();
            $table->integer('litros');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('marca_id')->references('id')->on('marcas');
            $table->foreign('categoria_id')->references('id')->on('categorias');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('articulos');
    }
}
