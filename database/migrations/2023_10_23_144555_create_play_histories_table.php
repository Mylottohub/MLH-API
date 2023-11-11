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
        Schema::create('play_histories', function (Blueprint $table) {
        $table->id();
        $table->unsignedBigInteger('operator');
        $table->unsignedBigInteger('gameid');
        $table->integer('num1')->nullable();
        $table->integer('num2')->nullable();
        $table->integer('num3')->nullable();
        $table->integer('num4')->nullable();
        $table->integer('num5')->nullable();
        $table->integer('num6')->nullable();
        $table->integer('num7')->nullable();
        $table->integer('num8')->nullable();
        $table->integer('num9')->nullable();
        $table->integer('num10')->nullable();
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
        $table->integer('result')->nullable();
        $table->double('winamount')->default(0);
        $table->string('gamename')->nullable();
        $table->string('gamecode')->nullable();
        $table->string('gametype')->nullable();
        $table->string('typecode')->nullable();
        $table->unsignedBigInteger('pid');
        $table->unsignedBigInteger('bid');
        $table->double('amount')->default(0);
        $table->double('stake')->default(0);
        $table->integer('line')->nullable();
        $table->double('b2')->default(0);
        $table->double('b3')->default(0);
        $table->double('b4')->default(0);
        $table->double('b5')->default(0);
        $table->unsignedBigInteger('dailygameid');
        $table->string('wintype')->nullable();
        $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('play_histories');
    }
};
