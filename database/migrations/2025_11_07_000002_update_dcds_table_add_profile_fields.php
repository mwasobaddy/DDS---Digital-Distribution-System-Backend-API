<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('dcds', function (Blueprint $table) {
            $table->string('national_id')->nullable()->after('referring_da_id');
            $table->date('dob')->nullable()->after('national_id');
            $table->string('gender')->nullable()->after('dob');
            $table->string('country')->nullable()->after('gender');
            $table->string('county')->nullable()->after('country');
            $table->string('sub_county')->nullable()->after('county');
            $table->string('ward')->nullable()->after('sub_county');
            $table->text('business_address')->nullable()->after('ward');

            $table->json('gps_location')->nullable()->after('business_address');

            $table->string('business_name')->nullable()->after('gps_location');
            $table->json('business_types')->nullable()->after('business_name');
            $table->string('daily_foot_traffic')->nullable()->after('business_types');
            $table->string('operating_hours')->nullable()->after('daily_foot_traffic');
            $table->json('preferred_campaign_types')->nullable()->after('operating_hours');
            $table->json('music_genres')->nullable()->after('preferred_campaign_types');

            $table->boolean('content_safe')->default(false)->after('music_genres');

            $table->string('preferred_wallet_type')->nullable()->after('content_safe');
            $table->string('wallet_pin_hash')->nullable()->after('preferred_wallet_type');

            $table->boolean('consent_terms')->default(false)->after('wallet_pin_hash');
            $table->boolean('consent_data')->default(false)->after('consent_terms');
        });
    }

    public function down()
    {
        Schema::table('dcds', function (Blueprint $table) {
            $table->dropColumn([
                'national_id','dob','gender','country','county','sub_county','ward','business_address',
                'gps_location','business_name','business_types','daily_foot_traffic','operating_hours',
                'preferred_campaign_types','music_genres','content_safe','preferred_wallet_type','wallet_pin_hash',
                'consent_terms','consent_data'
            ]);
        });
    }
};
