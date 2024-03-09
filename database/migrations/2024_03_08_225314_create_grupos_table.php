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
        Schema::create('grupos', function (Blueprint $table) {
            $table->id();
            $table->string('nome');
            $table->string('slug');
            $table->string('moeda');
            $table->bigInteger('moeda_id')->unsigned();
            $table->timestamps();
        });

        Schema::table('grupos', function (Blueprint $table) {
            $table->index('moeda_id');
            $table->foreign('moeda_id')->references('id')->on('moedas')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('grupos');
    }

};
