<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('dcds', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('referral_code')->unique();
            $table->unsignedBigInteger('user_id');
            $table->string('qr_code')->nullable();
            $table->unsignedBigInteger('referring_da_id')->nullable();
            $table->enum('status', ['pending', 'active', 'inactive'])->default('pending');
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('referring_da_id')->references('id')->on('das');
        });
    }

    public function down()
    {
        Schema::dropIfExists('dcds');
    }
};