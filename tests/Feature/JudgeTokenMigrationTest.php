<?php

namespace Tests\Feature;

use Illuminate\Database\Migrations\DatabaseMigrationRepository;
use Illuminate\Database\Migrations\Migrator;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class JudgeTokenMigrationTest extends TestCase
{
    private const DROP_MIGRATION = '2026_11_01_000000_drop_judge_token_from_solutions_table';

    public function testFreshSchemaDoesNotHaveJudgeTokenColumn()
    {
        $this->assertFalse(Schema::hasColumn('solutions', 'judge_token'));
    }

    public function testDropRemovesExistingJudgeTokenColumn()
    {
        $this->withIsolatedSchema(function (Blueprint $table) {
            $table->increments('id');
            $table->string('judge_token', 80)->nullable();
        }, function (Migrator $migrator) {
            $this->assertTrue(Schema::hasColumn('solutions', 'judge_token'));

            $migrator->run([$this->migrationPath(self::DROP_MIGRATION)]);

            $this->assertFalse(Schema::hasColumn('solutions', 'judge_token'));
        });
    }

    public function testDropIsNoOpWhenJudgeTokenColumnIsMissing()
    {
        $this->withIsolatedSchema(function (Blueprint $table) {
            $table->increments('id');
        }, function (Migrator $migrator) {
            $migrator->run([$this->migrationPath(self::DROP_MIGRATION)]);

            $this->assertFalse(Schema::hasColumn('solutions', 'judge_token'));
        });
    }

    public function testDropRollbackDoesNotRestoreJudgeTokenColumn()
    {
        $this->withIsolatedSchema(function (Blueprint $table) {
            $table->increments('id');
            $table->string('judge_token', 80)->nullable();
        }, function (Migrator $migrator) {
            $migrator->run([$this->migrationPath(self::DROP_MIGRATION)]);
            $this->assertFalse(Schema::hasColumn('solutions', 'judge_token'));

            $migrator->rollback([$this->migrationPath(self::DROP_MIGRATION)]);

            $this->assertFalse(Schema::hasColumn('solutions', 'judge_token'));
        });
    }

    private function migrationPath(string $name): string
    {
        return database_path('migrations/'.$name.'.php');
    }

    private function withIsolatedSchema(callable $createSolutions, callable $assertions): void
    {
        $connection = 'judge_token_migration_test';
        config(['database.connections.'.$connection => [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]]);

        $repository = new DatabaseMigrationRepository($this->app['db'], 'migrations');
        $migrator = new Migrator($repository, $this->app['db'], $this->app['files'], $this->app['events']);

        try {
            $migrator->usingConnection($connection, function () use ($repository, $migrator, $createSolutions, $assertions) {
                $repository->createRepository();
                Schema::create('solutions', $createSolutions);
                $assertions($migrator);
            });
        } finally {
            DB::purge($connection);
        }
    }
}
