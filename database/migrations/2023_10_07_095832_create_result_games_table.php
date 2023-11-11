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
        Schema::connection('results')->create('result_games', function (Blueprint $table) {
            $table->id();
            $table->text('winning_number')->nullable();
            $table->text('machine_number')->nullable();
            $table->integer('game')->nullable();
            $table->integer('operator')->nullable();
            $table->string('date')->nullable();
            $table->string('year')->nullable();
            $table->integer('month')->nullable();
            $table->integer('winning_total')->nullable();
            $table->integer('machine_total')->nullable();
            $table->integer('winning_num1')->nullable();
            $table->integer('winning_num2')->nullable();
            $table->integer('winning_num3')->nullable();
            $table->integer('winning_num4')->nullable();
            $table->integer('winning_num5')->nullable();
            $table->integer('winning_num6')->nullable();
            $table->integer('machine_num1')->nullable();
            $table->integer('machine_num2')->nullable();
            $table->integer('machine_num3')->nullable();
            $table->integer('machine_num4')->nullable();
            $table->integer('machine_num5')->nullable();
            $table->integer('machine_num6')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('result_games');
    }
};
