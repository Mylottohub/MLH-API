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
        Schema::connection('results')->create('set_lotto_models', function (Blueprint $table) {
            $table->id();
            $table->text('num')->nullable();
            $table->text('date')->nullable();
            $table->text('username')->nullable();
            $table->unsignedBigInteger('user');
            $table->double('amount')->default(0);
            $table->double('stake')->default(0);
            $table->integer('line')->nullable();
            $table->string('mgametype')->nullable();
            $table->string('status')->nullable();
            $table->string('drawAlias')->nullable();
            $table->string('drawDate')->nullable();
            $table->unsignedBigInteger('drawId');
            $table->unsignedBigInteger('drawNumber');
            $table->string('drawStatusDesc')->nullable();
            $table->unsignedBigInteger('drawStatusId');
            $table->unsignedBigInteger('transaction_id');
            $table->double('totalAmount')->default(0);
            $table->string('wagerID')->nullable();
            $table->unsignedBigInteger('wagerType');
            $table->double('amount_won')->default(0);
            $table->string('fetch_date')->nullable();
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
        Schema::dropIfExists('set_lotto_models');
    }
};
