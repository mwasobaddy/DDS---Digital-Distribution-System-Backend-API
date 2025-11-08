<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('campaigns', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->unsignedBigInteger('client_id');
            $table->string('title');
            $table->string('campaign_type')->nullable();
            $table->string('objective')->nullable();
            $table->string('content_safety')->nullable();
            $table->json('business_types')->nullable();
            $table->text('description')->nullable();
            $table->string('product_url')->nullable(); // Added for DDS
            $table->string('explainer_video_url')->nullable();
            $table->decimal('budget', 15, 2);
            $table->decimal('rate_per_scan', 10, 2)->default(0); // Added for DDS
            $table->enum('status', ['DRAFT', 'SUBMITTED', 'UNDER_REVIEW', 'APPROVED', 'PAID', 'LIVE', 'COMPLETED', 'REJECTED'])->default('DRAFT');
            $table->json('target_counties')->nullable();
            $table->json('target_regions')->nullable();
            $table->timestamp('start_date')->nullable();
            $table->timestamp('end_date')->nullable();
            $table->timestamps();

            $table->foreign('client_id')->references('id')->on('users');
        });
    }

    public function down()
    {
        Schema::dropIfExists('campaigns');
    }
};