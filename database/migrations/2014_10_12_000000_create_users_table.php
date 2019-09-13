<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('username')->unique();
            $table->string('nick')->default('');
            $table->string('email')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->integer('submit')->default(0);
            $table->integer('solved')->default(0);
            $table->string('locale')->nullable();
            $table->string('school')->nullable();
            $table->integer('language')->nullable();
            $table->integer('status')->default(0);
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();

            $table->index('username');
            $table->index('email');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
