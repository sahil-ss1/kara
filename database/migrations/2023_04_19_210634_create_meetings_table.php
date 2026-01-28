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
        Schema::create('meetings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('manager_id');
            $table->unsignedBigInteger('target_id');
            $table->dateTime('startAt')->nullable();
            $table->dateTime('endAt')->nullable();
            $table->string('title');
            $table->string('feeling',50)->nullable();
            $table->string('status', 50)->default(\App\Enum\MeetingStatus::ACTIVE->value);
            $table->text('feeling_note')->nullable();
            $table->text('manager_note')->nullable();
            $table->text('target_note')->nullable();
            $table->string('google_event_id')->nullable();

            $table->foreign('manager_id')->references('id')->on('members')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('target_id')->references('id')->on('members')->onDelete('cascade')->onUpdate('cascade');

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
        Schema::dropIfExists('meetings');
    }
};
