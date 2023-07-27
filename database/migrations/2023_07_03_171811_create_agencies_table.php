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
        Schema::connection('agency')->create('agencies', function (Blueprint $table) {
            $table->increments('id');
            $table->string('username')->nullable();
            $table->string('name')->nullable();
            $table->string('dob')->nullable();
            $table->string('gender')->nullable();
            $table->string('tell')->nullable();
            $table->string('email');
            $table->string('password');
            $table->boolean('status');
            $table->string('confirmation_code')->nullable();
            $table->boolean('confirmed');
            $table->string('date')->nullable();
            $table->double('wallet')->default(0);
            $table->string('role')->nullable();
            $table->string('type')->nullable();
            $table->double('wwallet')->default(0);
            $table->double('bwallet')->default(0);
            $table->integer('bank')->nullable();
            $table->string('bname')->nullable();
            $table->string('accno')->nullable();
            $table->string('accname')->nullable();
            $table->string('state')->nullable();
            $table->string('pix')->nullable();
            $table->string('lga')->nullable();
            $table->string('country')->nullable();
            $table->integer('ref')->nullable();
            $table->integer('ccommission')->nullable();
            $table->integer('pcommission')->nullable();
            $table->integer('auser')->nullable();
            $table->integer('is_robot')->nullable();
            $table->text('games')->nullable();
            $table->string('num_pos')->nullable();
            $table->string('num_pos2')->nullable();
            $table->string('num_pos1')->nullable();
            $table->string('num_pos3')->nullable();
            $table->integer('ussd_action')->nullable();
            $table->dateTime('site_time')->nullable();
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->text('address')->nullable();
            $table->double('gl_bwallet')->default(0);
            $table->double('sl_bwallet')->default(0);
            $table->double('gh_bwallet')->default(0);
            $table->double('lm_bwallet')->default(0);
            $table->double('we_bwallet')->default(0);
            $table->timestamp('email_verified_at')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('agencies');
    }
};
