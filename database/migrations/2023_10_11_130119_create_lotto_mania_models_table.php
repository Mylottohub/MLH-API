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
        Schema::connection('results')->create('lotto_mania_models', function (Blueprint $table) {
            $table->id();
            $table->text('num')->nullable();
            $table->text('date')->nullable();
            $table->text('username')->nullable();
            $table->unsignedBigInteger('user');
            $table->double('amount')->default(0);
            $table->double('stake')->default(0);
            $table->integer('line')->nullable();
            $table->integer('GameType')->nullable();
            $table->string('GameTypeName')->nullable();
            $table->unsignedBigInteger('GameId');
            $table->string('GameName')->nullable();
            $table->dateTime('DrawTime')->nullable();
            $table->string('TranId')->nullable();
            $table->string('TSN')->nullable();
            $table->double('WinAmount')->default(0);
            $table->integer('WinStatus')->nullable();
            $table->string('SessionId')->nullable();
            $table->double('balance')->default(0);
            $table->string('SelectionType')->nullable();
            $table->string('mgametype')->nullable();
            $table->string('operator_type')->nullable();
            $table->string('status')->nullable();
            $table->integer('double_chance')->nullable();
            $table->string('user_type')->nullable();
            $table->string('customer_tell')->nullable();
            $table->double('commission')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lotto_mania_models');
    }
};
