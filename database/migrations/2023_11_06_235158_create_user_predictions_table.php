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
        Schema::connection('results')->create('user_predictions', function (Blueprint $table) {
            $table->id();
            $table->integer('operator')->nullable();
            $table->integer('game')->nullable();
            $table->integer('num1')->nullable();
            $table->integer('num2')->nullable();
            $table->integer('num3')->nullable();
            $table->integer('num4')->nullable();
            $table->integer('num5')->nullable();
            $table->text('date')->nullable();
            $table->text('username')->nullable();
            $table->unsignedBigInteger('user');
            $table->integer('num1result')->nullable();
            $table->integer('num2result')->nullable();
            $table->integer('num3result')->nullable();
            $table->integer('num4result')->nullable();
            $table->integer('num5result')->nullable();
            $table->integer('num1machine')->nullable();
            $table->integer('num2machine')->nullable();
            $table->integer('num3machine')->nullable();
            $table->integer('num4machine')->nullable();
            $table->integer('num5machine')->nullable();
            $table->integer('totalpoint')->nullable();
            $table->integer('result')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_predictions');
    }
};
