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
        Schema::create('stages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pipeline_id')->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->string('hubspot_id');
            $table->string('hubspot_pipeline_id');
            $table->string('label');
            $table->integer('display_order');
            $table->dateTime('hubspot_createdAt');
            $table->dateTime('hubspot_updatedAt');
            $table->boolean('isClosed');
            $table->decimal('probability',8,2);
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
        Schema::dropIfExists('stages');
    }
};
