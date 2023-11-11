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
        Schema::connection('results')->table('lotto_mania_models', function (Blueprint $table) {
            $table->unsignedBigInteger('DrawId');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lotto_mania_models', function (Blueprint $table) {
            //
        });
    }
};
