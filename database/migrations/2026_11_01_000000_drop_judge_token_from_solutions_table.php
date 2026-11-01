<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DropJudgeTokenFromSolutionsTable extends Migration
{
    public function up()
    {
        if (! Schema::hasColumn('solutions', 'judge_token')) {
            return;
        }

        Schema::table('solutions', function (Blueprint $table) {
            $table->dropColumn('judge_token');
        });
    }

    public function down()
    {
        // 该列已废弃，回滚不再恢复。
    }
}
