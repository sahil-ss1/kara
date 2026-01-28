<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('forecast_categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->string('label');
            $table->string('internal_value');
            $table->integer('display_order');
            $table->timestamps();
        });

        Schema::table('deals', function (Blueprint $table) {
            $table->string('hs_manual_forecast_category')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('deals', function (Blueprint $table) {
            $table->dropColumn('hs_manual_forecast_category');
        });
        Schema::dropIfExists('forecast_categories');
    }
};
