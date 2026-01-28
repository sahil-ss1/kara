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
        Schema::create('deals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pipeline_id')->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('stage_id')->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('member_id')->nullable()->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->string('hubspot_id');
            $table->dateTime('hubspot_createdAt');
            $table->dateTime('hubspot_updatedAt');
            $table->string('name');
            $table->decimal('amount',10,2)->nullable();
            $table->dateTime('closedate')->nullable();
            $table->dateTime('createdate');
            $table->string('hubspot_pipeline_id');
            $table->string('hubspot_stage_id');
            $table->dateTime('hs_date_entered');
            $table->string('hubspot_owner_id')->nullable();
            $table->boolean('hs_is_closed')->default(false);
            $table->boolean('hs_is_closed_won')->default(false);
            $table->decimal('kara_probability')->nullable();
            $table->string('hs_next_step')->nullable();
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
        Schema::dropIfExists('deals');
    }
};
