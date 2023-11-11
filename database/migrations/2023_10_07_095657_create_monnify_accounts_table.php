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
        Schema::create('monnify_accounts', function (Blueprint $table) {
            $table->id();
            $table->string('contractCode')->nullable();
            $table->string('accountReference')->nullable();
            $table->string('accountName')->nullable();
            $table->string('currencyCode')->nullable();
            $table->string('customerEmail')->nullable();
            $table->string('accountNumber')->nullable();
            $table->string('bankName')->nullable();
            $table->string('bankCode')->nullable();
            $table->string('reservationReference')->nullable();
            $table->string('status')->nullable();
            $table->string('createdOn')->nullable();
            $table->unsignedBigInteger('user');
            $table->integer('megzy')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('monnify_accounts');
    }
};
