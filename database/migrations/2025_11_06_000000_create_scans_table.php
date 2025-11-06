<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('scans', function (Blueprint $table) {
            $table->id();
            $table->string('qr_code')->index();
            $table->unsignedBigInteger('dcd_id')->index();
            $table->unsignedBigInteger('campaign_id')->index();
            $table->string('device_id')->index(); // Fraud prevention
            $table->string('ip_address')->nullable();
            $table->text('user_agent')->nullable();
            $table->decimal('earnings_amount', 10, 2)->default(0);
            $table->boolean('earnings_processed')->default(false);
            $table->json('location')->nullable(); // {lat, lng, city, country}
            $table->timestamps();

            // Index for duplicate detection
            $table->index(['device_id', 'campaign_id', 'created_at']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('scans');
    }
};