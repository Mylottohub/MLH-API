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
        Schema::connection('transaction')->create('transactions', function (Blueprint $table) {
            $table->id();
            $table->double('amount')->default(0);
            $table->string('date')->nullable();
            $table->unsignedBigInteger('user');
            $table->string('type')->nullable();
            $table->text('description')->nullable();
            $table->string('username')->nullable();
            $table->string('channel')->nullable();
            $table->string('ref')->nullable();
            $table->string('ref2')->nullable();
            $table->double('abalance')->nullable();
            $table->string('gameIdNumber')->nullable();
            $table->string('gamePlayId')->nullable();
            $table->string('user_type')->nullable();
            $table->string('customer_tell')->nullable();
            $table->double('commission')->nullable();
            $table->timestamps();
          
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
