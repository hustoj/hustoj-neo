<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateTopicTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('topics', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title');
            $table->integer('user_id');
            $table->integer('contest_id')->default(0);
            $table->integer('problem_id')->nullable();
            $table->text('content');
            $table->softDeletes();
            $table->timestamps();

            $table->index('user_id');
            $table->index('problem_id');
        });

        Schema::create('replies', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id');
            $table->integer('topic_id');
            $table->text('content');
            $table->timestamps();
            $table->softDeletes();

            $table->index('topic_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('topics');
        Schema::drop('replies');
    }
}
