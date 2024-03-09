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
        Schema::create('moedas', function (Blueprint $table) {
            $table->id()->autoIncrement();
            $table->string('name');
            $table->string('symbol');
            $table->string('slug');
            $table->integer('ranking');
            $table->decimal('market_cap',30,8)->nullable();
            $table->decimal('price',30,8)->nullable();
            $table->decimal('volume_24h',30,8)->nullable();
            $table->decimal('variacao_24h',30,8)->nullable();
            $table->json('dados')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('moedas');
    }
};
