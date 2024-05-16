<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('pets_instituicao', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('instituicao_id');
            $table->foreign('instituicao_id')->references('id')->on('instituicoes')->onDelete('cascade');
            $table->string('nome');
            $table->string('porte');
            $table->string('pelagem');
            $table->string('foto');
            $table->string('especie');
            $table->string('outra_especie')->nullable();
            $table->string('raca')->nullable();
            $table->string('encontrado')->nullable();
            $table->text('descricao')->nullable();
            $table->string('status')->default('Aguardando');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pets_instituicao');
    }
};
