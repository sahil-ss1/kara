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
        Schema::create('goals', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('member_id')->nullable()->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('team_id')->nullable()->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('pipeline_id')->nullable()->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('stage_id')->nullable()->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->string('type', 50); //Deal,Task,Call,Meeting,Deadline,Email,Lunch,Note
            //$table->string('activity_type', 50)->nullable(); //Task,Call,Meeting,Deadline,Email,Lunch,Note
            $table->string('type_status', 50)->nullable(); //Created,Won,Lost,InProgress,Open,Closed
            //$table->enum('activity_status', ['Created','Done','InProgress'])->nullable();
            $table->string('stage_status', 50)->default(\App\Enum\GoalStageStatus::NONE->value); //MovedIn,MovedOut,InStage,None
            $table->string('metric', 50); //Value,Count
            $table->string('interval', 50); //Week,Month,Quarter,Year,Once
            $table->dateTime('start_date');
            $table->dateTime('end_date')->nullable();
            $table->decimal('value',10,2);
            $table->boolean('active')->default(1);
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
        Schema::dropIfExists('goals');
    }
};
