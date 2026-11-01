<?php

namespace Tests\Unit;

use Illuminate\Contracts\Console\Kernel;
use Tests\TestCase;

/**
 * Laravel 11+/13 新骨架移除了 app/Console/Kernel.php，改为由 Application::configure()
 * 默认链 withCommands() 自动扫描 app/Console/Commands 目录。
 *
 * 本测试固化该约定：确保仓库内所有自定义 Artisan 命令在新骨架下仍被正确注册，
 * 防止后续重构 bootstrap/app.php 时误删自动加载能力而导致命令静默丢失。
 */
class ConsoleCommandsRegistrationTest extends TestCase
{
    /**
     * 仓库内声明的自定义命令 signature 清单（不含框架自带命令）。
     * 8 个直接继承 Command 的命令 + 1 个 app/Console/Commands/Once 下的命令。
     * app/Console/Commands/Migrations/* 继承抽象类 Migration，本身不是命令。
     */
    private const EXPECTED_COMMANDS = [
        'assign:admin',
        'database:migrate',
        'fix:user:submits',
        'judger:new',
        'task:make',
        'user:access-time',
    ];

    public function testCustomArtisanCommandsAreRegistered()
    {
        $kernel = $this->app->make(Kernel::class);
        $registered = $kernel->all();

        foreach (self::EXPECTED_COMMANDS as $signature) {
            $this->assertArrayHasKey(
                $signature,
                $registered,
                "自定义命令 [{$signature}] 未被注册，检查 bootstrap/app.php 是否保留 withCommands() 自动加载。"
            );
        }
    }
}
