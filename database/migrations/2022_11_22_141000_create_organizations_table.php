<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('organizations', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('currency', 3);
            $table->string('timezone')->nullable();
            $table->boolean('synchronizing')->default(false);
            $table->dateTime('last_sync')->nullable();
            $table->string('hubspot_uiDomain')->nullable();
            $table->string('hubspot_portalId')->nullable();
            $table->integer('warn_last_activity_days')->default(30);
            $table->integer('warn_stage_time_days')->default(30);
            $table->integer('warn_creation_time_days')->default(180);
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('organizations');
    }
};
