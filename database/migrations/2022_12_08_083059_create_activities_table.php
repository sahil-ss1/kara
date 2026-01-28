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
        Schema::create('activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('deal_id')->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('member_id')->nullable()->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->string('hubspot_id');
            $table->string('type');
            $table->string('hubspot_owner_id')->nullable();
            $table->string('hubspot_deal_id')->nullable();
            $table->dateTime('hubspot_createdAt');
            $table->dateTime('hubspot_updatedAt');
            $table->dateTime('hubspot_timestamp');
            $table->string('hubspot_status');
            $table->dateTime('hubspot_task_completion_date')->nullable();
            $table->string('hubspot_task_subject')->nullable();
            $table->text('hubspot_task_body')->nullable();
            $table->string('hubspot_task_type')->nullable();
            $table->string('hubspot_task_priority')->nullable();

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
        Schema::dropIfExists('activities');
    }
};
