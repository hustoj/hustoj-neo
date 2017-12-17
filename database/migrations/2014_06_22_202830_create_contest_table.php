<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateContestTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('contests', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title');
            $table->integer('status')->default(1);
            $table->boolean('private')->default(false);
            $table->text('description');
            $table->dateTime('start_time');
            $table->dateTime('end_time');
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('contest_problem', function (Blueprint $table) {
            $table->integer('contest_id');
            $table->integer('problem_id');
            $table->string('title');
            $table->integer('order');

            $table->unique(['contest_id', 'problem_id']);
        });

        Schema::create('contest_user', function (Blueprint $table) {
            $table->integer('contest_id');
            $table->integer('user_id');
            $table->timestamps();

            $table->unique(['contest_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('contests');
        Schema::drop('contest_problem');
        Schema::drop('contest_user');
    }
}
