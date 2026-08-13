<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddJudgeTokenToSolutionsTable extends Migration
{
    public function up()
    {
        Schema::table('solutions', function (Blueprint $table) {
            $table->string('judge_token', 80)->nullable()->after('judged_at');
        });
    }

    public function down()
    {
        Schema::table('solutions', function (Blueprint $table) {
            $table->dropColumn('judge_token');
        });
    }
}
