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
        Schema::create('operators', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('logo')->nullable();
            $table->string('del')->nullable();
            $table->string('play')->nullable();
            $table->string('plink')->nullable();
            $table->text('feature')->nullable();
            $table->string('operator_link')->nullable();
            $table->integer('play_order')->nullable();
            $table->string('play_type')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('operators');
    }
};
