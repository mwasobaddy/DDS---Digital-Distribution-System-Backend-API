<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('das', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('referral_code')->unique();
            $table->decimal('venture_shares', 15, 2)->default(0);
            
            // Personal Information
            $table->string('national_id')->unique();
            $table->date('dob');
            $table->enum('gender', ['male', 'female']);
            
            // Location
            $table->string('country');
            $table->string('county');
            $table->string('sub_county');
            $table->string('ward');
            $table->text('address');
            
            // Social Media
            $table->json('social_platforms')->nullable();
            $table->string('followers_range');
            $table->enum('preferred_channel', ['whatsapp', 'email', 'in-app']);
            
            // Wallet
            $table->enum('preferred_wallet_type', ['personal', 'business']);
            $table->string('wallet_pin_hash');
            
            // Consent
            $table->boolean('consent_terms')->default(false);
            $table->boolean('consent_data')->default(false);
            $table->boolean('consent_ethics')->default(false);
            
            // Referrals
            $table->unsignedBigInteger('referred_by_da_id')->nullable();
            $table->foreign('referred_by_da_id')->references('id')->on('das')->onDelete('set null');
            
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    public function down()
    {
        Schema::dropIfExists('das');
    }
};