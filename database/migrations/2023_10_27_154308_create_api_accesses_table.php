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
        Schema::create('api_accesses', function (Blueprint $table) {
            $table->id();
            $table->string('merchant');
            $table->string('username');
            $table->string('password');
            $table->unsignedBigInteger('user_code');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('api_accesses');
    }
};
