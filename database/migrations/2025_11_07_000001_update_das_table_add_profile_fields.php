<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('das', function (Blueprint $table) {
            $table->string('national_id')->nullable()->after('referral_code');
            $table->date('dob')->nullable()->after('national_id');
            $table->string('gender')->nullable()->after('dob');
            $table->string('country')->nullable()->after('gender');
            $table->string('county')->nullable()->after('country');
            $table->string('sub_county')->nullable()->after('county');
            $table->string('ward')->nullable()->after('sub_county');
            $table->text('address')->nullable()->after('ward');

            $table->json('social_platforms')->nullable()->after('address');
            $table->string('followers_range')->nullable()->after('social_platforms');
            $table->string('preferred_channel')->nullable()->after('followers_range');

            $table->string('preferred_wallet_type')->nullable()->after('preferred_channel');
            $table->string('wallet_pin_hash')->nullable()->after('preferred_wallet_type');

            $table->boolean('consent_terms')->default(false)->after('wallet_pin_hash');
            $table->boolean('consent_data')->default(false)->after('consent_terms');
            $table->boolean('consent_ethics')->default(false)->after('consent_data');

            $table->unsignedBigInteger('referred_by_da_id')->nullable()->after('venture_shares');
            $table->foreign('referred_by_da_id')->references('id')->on('das');
        });
    }

    public function down()
    {
        Schema::table('das', function (Blueprint $table) {
            $table->dropForeign(['referred_by_da_id']);
            $table->dropColumn([
                'national_id','dob','gender','country','county','sub_county','ward','address',
                'social_platforms','followers_range','preferred_channel',
                'preferred_wallet_type','wallet_pin_hash',
                'consent_terms','consent_data','consent_ethics','referred_by_da_id'
            ]);
        });
    }
};
