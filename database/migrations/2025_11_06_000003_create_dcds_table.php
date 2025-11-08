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
            
            // Personal Information
            $table->string('national_id')->nullable();
            $table->date('dob')->nullable();
            $table->string('gender')->nullable();
            
            // Location
            $table->string('country')->nullable();
            $table->string('county')->nullable();
            $table->string('sub_county')->nullable();
            $table->string('ward')->nullable();
            $table->text('business_address')->nullable();
            $table->json('gps_location')->nullable();
            
            // Business Information
            $table->string('business_name')->nullable();
            $table->json('business_types')->nullable();
            $table->string('daily_foot_traffic')->nullable();
            $table->string('operating_hours')->nullable();
            $table->json('preferred_campaign_types')->nullable();
            $table->json('music_genres')->nullable();
            $table->boolean('content_safe')->default(false);
            
            // Wallet
            $table->string('preferred_wallet_type')->nullable();
            $table->string('wallet_pin_hash')->nullable();
            
            // Consent
            $table->boolean('consent_terms')->default(false);
            $table->boolean('consent_data')->default(false);
            
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