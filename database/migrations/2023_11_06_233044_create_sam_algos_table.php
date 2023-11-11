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
        Schema::connection('results')->create('sam_algos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('operator');
            $table->string('num')->nullable();
            $table->string('numresult')->nullable();
            $table->unsignedBigInteger('game');
            $table->dateTime('date')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sam_algos');
    }
};
