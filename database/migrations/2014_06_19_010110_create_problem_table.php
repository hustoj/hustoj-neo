<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProblemTable extends Migration
{
    protected $table = 'problems';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->table, function (Blueprint $table) {
            $table->increments('id');
            $table->string('title');
            $table->text('description');
            $table->text('input');
            $table->text('output');
            $table->text('sample_input');
            $table->text('sample_output');
            $table->text('hint')->nullable();
            $table->text('memo');
            $table->string('source')->nullable();
            $table->integer('status')->default(0);
            $table->integer('time_limit');
            $table->integer('memory_limit');
            $table->boolean('spj')->default(false);
            $table->integer('submit')->default(0);
            $table->integer('accepted')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });

        $statement = "ALTER TABLE {$this->table} AUTO_INCREMENT = 1000;";
        DB::unprepared($statement);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop($this->table);
    }
}
