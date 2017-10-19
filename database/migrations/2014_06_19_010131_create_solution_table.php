<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateSolutionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('solutions', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('problem_id');
            $table->integer('contest_id')->nullable();
            $table->integer('order');
            $table->integer('user_id');
            $table->integer('time_cost')->default(0);
            $table->integer('memory_cost')->default(0);
            $table->integer('language');
            $table->integer('result')->nullable();
            $table->integer('code_length');
            $table->string('ip');
            $table->timestamp('judged_at')->nullable();

            $table->timestamps();

            $table->index(['user_id', 'problem_id', 'language', 'result'], 'search');
            $table->index(['contest_id'], 'contest_id');
            $table->index(['time_cost'], 'time_cost');
        });

        Schema::create('compile_info', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('solution_id');
            $table->text('content')->nullable();
            $table->timestamps();

            $table->index('solution_id');
        });

        Schema::create('runtime_info', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('solution_id');
            $table->text('content')->nullable();
            $table->timestamps();

            $table->index('solution_id');
        });
        Schema::create('source_code', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('solution_id');
            $table->text('code')->nullable();

            $table->index('solution_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('solutions');
        Schema::drop('compile_info');
        Schema::drop('runtime_info');
        Schema::drop('source_code');
    }
}
