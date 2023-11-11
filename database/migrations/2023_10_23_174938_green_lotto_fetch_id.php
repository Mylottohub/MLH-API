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
        Schema::connection('results')->create('green_lotto_fetch_id', function (Blueprint $table) {
            $table->id();
            $table->string('fetch_id')->nullable();
            $table->unsignedBigInteger('result')->nullable();
  
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('green_lotto_fetch_id');
    }
};
