# 升级到 Laravel 13 骨架现代化方案

> 版本目标:框架已为 `laravel/framework v13.6.0`(2026‑03 发布的最新主版本),本次升级聚焦 **L10 骨架 → L11+ 极简骨架** 的迁移,以及随之而来的依赖/配置整理。
>
> 文档维护:升级期间出现新发现,直接追加到对应章节。

---

## 一、收益总览

1. **代码瘦身**:删除 ~200 行 `Kernel.php / Handler.php / 默认 Middleware` 模板代码。
2. **安全增强**:启用 L13 的 `PreventRequestForgery`(`Sec-Fetch-Site` 起源校验),零配置生效。
3. **可读性**:中间件、异常、路由配置集中到 `bootstrap/app.php`,所有运行期"装配"集中可见。
4. **现代化能力开闸**:`Cache::touch()`、`Queue::route()`、控制器 `#[Middleware]` 属性、`routes/console.php` 直写 `Schedule::command()`。
5. **长生命周期**:Laravel 13 bug 修到 2027 Q3、安全修到 2028 Q1。

---

## 二、变更清单(按执行顺序)

### A. 骨架文件

| 操作 | 文件 | 说明 |
|---|---|---|
| ✏️ 重写 | `bootstrap/app.php` | 改为 `Application::configure()->withRouting()->withMiddleware()->withExceptions()` 风格 |
| 🆕 新建 | `bootstrap/providers.php` | 显式登记应用 Provider,并保留 `App\Hustoj\Hashing\HashServiceProvider` 自定义哈希绑定 |
| ❌ 删除 | `app/Http/Kernel.php` | 由 `withMiddleware()` 替代 |
| ❌ 删除 | `app/Console/Kernel.php` | 由 `withRouting(commands: …)` + `routes/console.php` 替代 |
| ❌ 删除 | `app/Exceptions/Handler.php` | 由 `withExceptions()` 替代(`$dontReport` 列表迁移过去) |
| ❌ 删除 | `app/Providers/RouteServiceProvider.php` | 由 `withRouting(then: …)` 替代,namespace 前缀显式保留 |

### B. 中间件清理

> 凡是"默认实现 + 空 `$except`"的标准中间件全部删除;有自定义的保留并继续显式注册。

| 操作 | 文件 | 原因 |
|---|---|---|
| ❌ 删除 | `app/Http/Middleware/EncryptCookies.php` | `$except` 为空,默认实现等价 |
| ❌ 删除 | `app/Http/Middleware/VerifyCsrfToken.php` | `$except` 为空,且 L11+ 走 `ValidateCsrfToken` 默认链 |
| ❌ 删除 | `app/Http/Middleware/TrimStrings.php` | `$except = ['password','password_confirmation']` 与 L11+ 默认完全一致 |
| ❌ 删除 | `app/Http/Middleware/Authenticate.php` | `redirectTo()` 行为与 L11+ 默认一致(都指向 `route('login')`) |
| ⚠️ **保留** | `app/Http/Middleware/TrustProxies.php` | `$headers` 含 `HEADER_X_FORWARDED_AWS_ELB`,需在 `withMiddleware()` 用 `replace()` 替换 Laravel 默认 `TrustProxies`,同时保留 L13 默认全局栈 |
| ⚠️ **保留** | `app/Http/Middleware/RedirectIfAuthenticated.php` | 自定义重定向到 `/home`,**非** L11+ 默认 |
| ⚠️ **保留** | `app/Http/Middleware/SetupConfig.php` | 仓库自有逻辑 |
| ⚠️ **保留** | `app/Http/Middleware/AuthorizeContest.php` | 业务中间件 |
| ⚠️ **保留** | `app/Http/Middleware/BackendAuthorize.php` | `admin` 组核心 |
| ⚠️ **保留** | `app/Http/Middleware/CompressContent.php` | 判题接口 `zip.response` 别名背后实现 |
| ⚠️ **保留** | `app/Http/Middleware/EnableCrossSite.php` | CORS 备用,与 `HandleCors` 互斥但不删 |

### C. 路由与命名空间

- **所有路由文件均使用字符串控制器写法**(如 `'Web\HomeController@index'`),依赖 `RouteServiceProvider::$namespace`。删除该 Provider 后必须在 `withRouting(then:)` 内用 `Route::group(['namespace' => 'App\Http\Controllers'], …)` 包裹三组路由(web / admin / judge)。
- `routes/judge.php` 当前是 `app('router')->...` 裸写法,**不能**塞进 `api:` 参数(会自动套 `throttle:api` 60/min)。继续保留为独立 `Route::prefix('judge')`,不加限流,不加 session。
- `routes/admin.php` 同上,挂到 `Route::prefix('admin')->middleware('admin')`。
- `routes/console.php` 保留原 `Artisan::command('inspire', …)`,后续如需调度,改用 `Schedule::command()` 直写。
- `routes/channels.php` 保留;由 `Application::configure()->withRouting(channels: ...)` 加载,不再注册 `BroadcastServiceProvider`。

### D. 中间件别名与组(`withMiddleware`)

需要在 `bootstrap/app.php` 注册:

```php
->withMiddleware(function (Middleware $middleware) {
    // 保留 Laravel 13 默认全局栈,只替换 TrustProxies 为自定义 header bitmask 版本
    $middleware->replace(
        \Illuminate\Http\Middleware\TrustProxies::class,
        \App\Http\Middleware\TrustProxies::class,
    );

    // web 组追加业务中间件
    $middleware->web(append: [
        \App\Http\Middleware\SetupConfig::class,
    ]);

    // admin 组(完全自定义,含 session/csrf + 后台鉴权)
    $middleware->group('admin', [
        \Illuminate\Cookie\Middleware\EncryptCookies::class,
        \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
        \Illuminate\Session\Middleware\StartSession::class,
        \Illuminate\View\Middleware\ShareErrorsFromSession::class,
        \Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class,
        \Illuminate\Routing\Middleware\SubstituteBindings::class,
        \App\Http\Middleware\BackendAuthorize::class,
    ]);

    // 路由别名
    $middleware->alias([
        'authorizeContest' => \App\Http\Middleware\AuthorizeContest::class,
        'zip.response'   => \App\Http\Middleware\CompressContent::class,
        'cors'           => \App\Http\Middleware\EnableCrossSite::class,
        'guest'          => \App\Http\Middleware\RedirectIfAuthenticated::class,
    ]);
})
```

### E. 异常处理(`withExceptions`)

迁移 `app/Exceptions/Handler.php::$dontReport`:

```php
->withExceptions(function (Exceptions $exceptions) {
    $exceptions->dontReport([
        \Illuminate\Auth\AuthenticationException::class,
        \Illuminate\Auth\Access\AuthorizationException::class,
        \Symfony\Component\HttpKernel\Exception\HttpException::class,
        \Illuminate\Database\Eloquent\ModelNotFoundException::class,
        \Illuminate\Session\TokenMismatchException::class,
        \Illuminate\Validation\ValidationException::class,
    ]);
})
```

### F. Providers

- `bootstrap/providers.php` 内容:
  ```php
  return [
      App\Providers\AppServiceProvider::class,
      App\Providers\AuthServiceProvider::class,
      App\Providers\CaptchaServiceProvider::class,
      App\Providers\EventServiceProvider::class,
      App\Hustoj\Hashing\HashServiceProvider::class,
  ];
  ```
- `RouteServiceProvider` 删除。
- `BroadcastServiceProvider` 不再注册;广播通道由 `bootstrap/app.php` 的 `withRouting(channels: __DIR__.'/../routes/channels.php')` 加载。`php artisan channel:list` 可能仍会提示未加载旧 Provider,但 `/broadcasting/auth` 路由和通道定义来自 `routes/channels.php`,运行时不依赖该 Provider。
- `HashServiceProvider` 必须用 `DeferrableProvider` 并同时提供 `hash` / `hash.driver`,否则 `Hash::driver()` 会回落到 Laravel 默认 `BcryptHasher`,旧 HUSTOJ 密码校验会失败。
- 其余 Provider 一律不动其内部逻辑。

### G. 依赖与配置

| 操作 | 文件 | 备注 |
|---|---|---|
| 校验 | `vladimir-yuldashev/laravel-queue-rabbitmq` | 验证 `^14.5` 已支持 L13;不支持则升 15.x |
| 校验 | `santigarcor/laratrust ^8.5` | 同上 |
| 校验 | `sentry/sentry-laravel ^4.25` | 4.25.x 已支持 L13（`illuminate/support: ^13.0`），使用 `Integration::handles($exceptions)` API 集成到 `bootstrap/app.php` |
| 整理 | `config/entrust.php` 与 `config/laratrust.php` | 二者并存,确认仅 laratrust 在用,entrust 可删(后续 PR) |

---

## 三、风险清单(按确定性排序)

### 🔴 确定性破坏点(必须前置处理)

1. **路由 namespace 丢失会全站 500**
   - 现状:`routes/web.php` / `admin.php` / `judge.php` 全部 `'Controller@method'` 字符串写法。
   - 处理:`withRouting(then:)` 内用 `Route::group(['namespace' => 'App\Http\Controllers'])` 显式包裹;每组路由独立 group。
   - 验证:`php artisan route:list` 必须输出无任何 `Target class … does not exist`。

2. **`admin` 中间件组完全自定义**
   - 现状:含 `Encrypt/Session/CSRF + BackendAuthorize`,L11+ 默认只有 `web/api` 两组。
   - 处理:在 `withMiddleware()` 用 `$middleware->group('admin', [...])` 显式构造,**漏写 StartSession 会让后台登录态全丢**。

3. **`routes/judge.php` 不能被自动加入 `api` 组**
   - 现状:判题接口当前**无任何中间件**(包括无 throttle、无 session)。
   - 处理:**禁止**用 `withRouting(api: 'routes/judge.php')`,改走 `then:` 内的 `Route::prefix('judge')->group(...)`。
   - 验证:`php artisan route:list --path=judge` 中间件列必须为空。

4. **`zip.response` 别名必须迁移**
   - 现状:`routes/judge.php` 用 `->middleware('zip.response')`,由 `App\Http\Kernel::$routeMiddleware` 注册。
   - 处理:必须在 `withMiddleware()->alias([...])` 中重建,否则判题数据接口 500(MiddlewareNotFound)。

5. **`guest` 别名(`RedirectIfAuthenticated`)**
   - 现状:重定向到 `/home`,非默认。
   - 处理:在 `alias` 中保留;**不能**改为 L11+ 默认的 `Authenticate::class`(默认指向 `/dashboard`)。

6. **`TrustProxies::$headers` 含 AWS_ELB 位**
   - 处理:保留 `app/Http/Middleware/TrustProxies.php` 文件,在 `withMiddleware()` 中用 `$middleware->replace(\Illuminate\Http\Middleware\TrustProxies::class, \App\Http\Middleware\TrustProxies::class)` 替换默认项。
   - 注意:不要用 `$middleware->use([...])` 全量覆盖 L13 默认全局栈,否则会漏掉 `ValidatePathEncoding` / `InvokeDeferredCallbacks` 等框架默认 middleware。

7. **旧 HUSTOJ 密码哈希必须覆盖 `hash.driver`**
   - 现状:业务代码直接用 `app('hash')->make()` / `check()`,旧密码不是 Laravel 默认 bcrypt 格式。
   - 处理:`App\Hustoj\Hashing\HashServiceProvider` 同时绑定 `hash` 和 `hash.driver`,并在 `provides()` 中声明二者。
   - 验证:`app('hash')` 与 `app('hash.driver')` 都必须解析为 `App\Hustoj\Hashing\Hasher`。

### 🟡 协议层潜在影响(需在 staging 回归)

8. **Carbon 时间序列化默认带微秒**(L11+)
   - 风险:判题接口返回 / Vue 前端 / 第三方对接拿到 `2026-05-21T10:00:00.123456Z`,字符串比较会失败。
   - 处理:在 `AppServiceProvider::boot()` 加 `Date::use(...)` 或在 `Solution / User` 模型 `$casts = ['created_at' => 'datetime:Y-m-d H:i:s']` 锁住老格式;**保留为升级后第一个回归点**。
   - 影响文件:`app/Entities/Solution.php`、`app/Entities/User.php`、判题端 ApiController。

9. **`PreventRequestForgery` 起源校验**
   - 风险:CDN/反向代理若改写 `Origin` 或剥掉 `Sec-Fetch-Site`,前端 POST 会 419。
   - 处理:升级后**真实域名压一遍登录 / 提交代码 / 发题解 / 管理后台编辑**。
   - 回退:若误伤,临时 `$middleware->validateCsrfTokens()` 退回到纯 token 模式。

10. **Job payload 序列化版本**
   - 风险:升级窗口期 RabbitMQ 队列中残留的旧 payload 反序列化失败。
   - 处理:**升级前清空 / 排空 `default` 与 judge 队列**;或先升级 producer、保留旧 consumer 直到队列排空(本仓库 consumer 是 hustoj 判题守护进程,不依赖 Laravel,**无此问题**)。

11. **MariaDB 驱动分离**
    - 风险:`DB_CONNECTION=mysql` 跑在 MariaDB 上时,部分 schema 操作行为差异。
    - 处理:本次不强制改;后续 PR 给 `config/database.php` 增 `mariadb` connection。

### 🟢 已确认无风险(已 grep 过)

- `VerifyCsrfToken::$except` 为空,无豁免路径丢失。
- `Authenticate::redirectTo()` 等价于 L11+ 默认实现。
- `TrimStrings::$except` 与默认完全一致。
- `auth.php` 中 `'table' => 'password_resets'` 已**显式**指向老表名,密码重置流程不受影响。
- 判题机经 RabbitMQ + MySQL `solution` 表协作,**表结构 / 字段 / 状态码全程不变**。

---

## 四、回归测试矩阵

| 路径 | 关键点 | 命令 / 操作 |
|---|---|---|
| `vendor/bin/phpunit` | 全部单元/Feature/Api 测试 | 必须全绿 |
| `php artisan route:list` | 无 NotFound 控制器、admin 组挂载正确、judge 无 middleware | 目视 + grep |
| 前台首页 `/` | session/cookie 正常,Bootstrap 分页样式正常 | 浏览器 |
| 登录 `/login` → `/home` | RedirectIfAuthenticated 生效 | 浏览器 |
| 后台 `/admin/home` | BackendAuthorize 拦截非管理员 | 浏览器 |
| 提交代码 → `solution` 表 → 判题机 | RabbitMQ payload 入队、judge 读取 OK | tail log + RabbitMQ 管理台 |
| `/judge/api/data` | `CompressContent` 生效(响应头 `Content-Encoding: gzip`) | curl |
| 比赛模式 `AuthorizeContest` | 未授权用户被拦截 | 浏览器 |

---

## 五、执行步骤

```
Step 1: 跑一次基线测试           → vendor/bin/phpunit
Step 2: 新增 bootstrap/providers.php
Step 3: 重写 bootstrap/app.php
Step 4: 删除 Kernel/Handler/RouteServiceProvider 与冗余中间件
Step 5: 清空 bootstrap/cache/*.php,composer dump-autoload -o
Step 6: 删除 config/app.php 的 providers + aliases 段
Step 7: 跑 php artisan route:list 验证三组挂载
Step 8: 再跑 vendor/bin/phpunit
```

---

## 六、执行结果(2026-05-21)

### 已完成

| 项 | 状态 |
|---|---|
| 基线测试 | ✅ 8/8 全绿 |
| `bootstrap/providers.php` 新建 | ✅(含 `App\Hustoj\Hashing\HashServiceProvider` 自定义哈希) |
| `bootstrap/app.php` 重写 | ✅ |
| 删除 `app/Http/Kernel.php` | ✅ |
| 删除 `app/Console/Kernel.php` | ✅ |
| 删除 `app/Exceptions/Handler.php` | ✅ |
| 删除 `app/Providers/RouteServiceProvider.php` | ✅ |
| 删除标准 Middleware × 4 | ✅(`EncryptCookies`、`VerifyCsrfToken`、`TrimStrings`、`Authenticate`) |
| `config/app.php` 清理 `providers` / `aliases` 段 | ✅ |
| `composer dump-autoload -o` | ✅ |
| `php artisan route:list` 全量路由 | ✅(无 ClassNotFound) |
| judge 路由中间件验证 | ✅ 仅 `zip.response`,无 throttle / session |
| admin 路由中间件验证 | ✅ 挂 `admin` 组 |
| web 首页中间件验证 | ✅ 挂 `web` 组(含追加 `SetupConfig`) |
| `php artisan config:cache` | ✅ |
| `php artisan about` | ✅ Laravel 13.6.0 运行正常 |
| `vendor/bin/phpunit` 升级后 | ✅ 8/8 全绿 |

### 既有问题(非本次引入)

- ~~**`php artisan route:cache` 失败**~~ — 已修复:`user.edit` / `user.password` GET/POST 重名,POST 路由分别改为 `user.update` / `user.password.update`。

### 待做(后续 PR)

1. **Carbon 时间序列化回归** — 抽样判题接口和模板,确认前端拿到的 datetime 字符串没变成微秒格式;必要时在 `AppServiceProvider::boot()` 锁死格式。
2. **`PreventRequestForgery` staging 验证** — 真实域名压一遍登录 / 提交代码 / 后台编辑表单。
3. **依赖版本审计** — `laratrust`、`vladimir-yuldashev/laravel-queue-rabbitmq`、`sentry-laravel` 是否有针对 L13 的更新分支。
4. ~~**路由命名冲突修复**~~ — 已完成,`route:cache` 可用。
5. **(可选)路由文件控制器写法现代化** — 把字符串控制器改为 `[Controller::class, 'method']`,**之后可删除 `bootstrap/app.php` 中 `withRouting(then:)` 内的 `Route::namespace()` 包裹**。
6. **(可选)`config/entrust.php` 清理** — 与 `config/laratrust.php` 并存,确认仅 laratrust 在用后删除。
