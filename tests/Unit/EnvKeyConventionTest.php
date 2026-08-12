<?php

namespace Tests\Unit;

use Tests\TestCase;

/**
 * Laravel 11+/13 配置键约定回归测试。
 *
 * 升级到新骨架后，框架默认读 CACHE_STORE / MAIL_MAILER 等新键名；
 * RabbitMQ 包读 RABBITMQ_USER 而非 RABBITMQ_LOGIN。
 * 历史上 .env.example / .env.testing 曾用过旧键，导致值不被读取而静默回退默认。
 * 本测试固化"配置文件读取的 env 键 == 文档约定键"，防止回退。
 */
class EnvKeyConventionTest extends TestCase
{
    public function testCacheStoreKeyIsReadByDefault()
    {
        // config/cache.php 应读 CACHE_STORE（Laravel 11+ 新骨架约定），而非旧 CACHE_DRIVER
        $default = config('cache.default');
        // 测试环境 .env.testing 设 CACHE_STORE=array
        $this->assertSame('array', $default, 'cache.default 未读到 CACHE_STORE，可能仍指向旧 CACHE_DRIVER。');
    }

    public function testRabbitmqCredentialsKeyMatchesPackageConvention()
    {
        // vladimir-yuldashev/laravel-queue-rabbitmq 读 RABBITMQ_USER（非 RABBITMQ_LOGIN）
        $config = config('queue.connections.rabbitmq.hosts.0');

        $this->assertArrayHasKey('user', $config);
        $this->assertArrayHasKey('password', $config);

        // 护栏逻辑：.env.testing 显式设 RABBITMQ_USER=（空字符串）。
        // 配置读 RABBITMQ_USER 时得到 ''；若有人改回旧键 RABBITMQ_LOGIN，
        // 则 .env.testing 的 RABBITMQ_USER 不再生效、RABBITMQ_LOGIN 又缺失，
        // 会回退默认 'guest' —— 此时下面的断言失败，提醒键名回退。
        $this->assertSame('', $config['user'], 'RabbitMQ 未读取 RABBITMQ_USER 键（疑似回退到 RABBITMQ_LOGIN 默认 guest）。');
    }

    public function testMailDefaultKeyMatchesFramework()
    {
        // config/mail.php 读 MAIL_MAILER；.env.testing 设 MAIL_MAILER=array
        $this->assertSame('array', config('mail.default'), 'mail.default 未读到 MAIL_MAILER。');
    }
}
