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
        Schema::create('members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->string('hubspot_id');
            $table->string('email');
            $table->string('firstName');
            $table->string('lastName');
            $table->dateTime('hubspot_createdAt');
            $table->dateTime('hubspot_updatedAt');
            $table->boolean('active')->default(true);
            $table->boolean('hubspot_archived')->default(false);
            $table->timestamps();

            $table->unique(['organization_id', 'email']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('members');
    }
};
