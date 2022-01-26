<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCompaniesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            $table->string('cnpj')->unique();
            
            $table->string('razao_social');
            $table->string('nome_fantasia');
            $table->string('atividade_principal');
            $table->date('data_abertura');
            $table->string('natureza_juridica');
            $table->string('cep');
            $table->string('logradouro');
            $table->integer('codigo_ibge');
            $table->string('cidade');
            $table->string('estado');
            $table->string('pais');

            $table->softDeletes();
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
        Schema::dropIfExists('companies');
    }
}
