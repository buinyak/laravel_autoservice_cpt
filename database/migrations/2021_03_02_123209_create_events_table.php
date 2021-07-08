<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEventsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->string('event_name',255)->nullable('false');
            $table->string('event_info',255)->nullable('false');
            $table->string('event_type',255)->nullable('false');
            $table->string('event_date',255)->nullable('false');
            $table->string('event_sum',255)->nullable('false');
            $table->integer('user_id')->nullable('false');
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
        Schema::dropIfExists('events');
    }
}
