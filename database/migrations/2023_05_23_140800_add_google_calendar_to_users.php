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
        Schema::table('users', function (Blueprint $table) {
            $table->json('google_token')->nullable();
            $table->json('google_refresh_token')->nullable();
            $table->string('google_calendar_id')->nullable();
            $table->string('google_name')->nullable();
            $table->string('timezone')->default('UTC');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('google_token');
            $table->dropColumn('google_refresh_token');
            $table->dropColumn('google_name');
            $table->dropColumn('google_calendar_id');
            $table->dropColumn('timezone');
        });
    }
};
