<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('earnings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('scan_id')->nullable();
            $table->decimal('amount', 10, 2);
            $table->enum('type', ['scan', 'commission', 'bonus']);
            $table->enum('status', ['pending', 'paid', 'cancelled'])->default('pending');
            $table->string('period'); // YYYY-MM
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('scan_id')->references('id')->on('scans');
        });
    }

    public function down()
    {
        Schema::dropIfExists('earnings');
    }
};