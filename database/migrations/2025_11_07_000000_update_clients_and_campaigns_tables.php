<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->string('account_type')->nullable()->after('company_name');
            $table->string('country')->nullable()->after('account_type');
            $table->string('referral_code')->nullable()->after('country');
            $table->string('contact_person')->nullable()->after('referral_code');
        });

        Schema::table('campaigns', function (Blueprint $table) {
            $table->string('campaign_type')->nullable()->after('title');
            $table->string('explainer_video_url')->nullable()->after('product_url');
            $table->string('objective')->nullable()->after('campaign_type');
            $table->string('content_safety')->nullable()->after('objective');
            $table->json('business_types')->nullable()->after('content_safety');
            $table->json('target_regions')->nullable()->after('target_counties');
        });
    }

    public function down()
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->dropColumn(['account_type', 'country', 'referral_code', 'contact_person']);
        });

        Schema::table('campaigns', function (Blueprint $table) {
            $table->dropColumn(['campaign_type', 'explainer_video_url', 'objective', 'content_safety', 'business_types', 'target_regions']);
        });
    }
};
