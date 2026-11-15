<?php
declare(strict_types=1);

namespace Core;

use Core\Ai\AiGateway;
use Core\Contracts\AiGatewayContract;
use Core\Contracts\AuditContract;
use Core\Contracts\AuthContract;
use Core\Contracts\CompanyContract;
use Core\Contracts\DynamicFieldContract;
use Core\Contracts\NotifierContract;
use Core\Contracts\PermissionContract;
use Core\Contracts\PrintContract;
use Core\Contracts\WorkflowContract;
use Core\Database\Connection;
use Core\Event\EventBus;
use Core\Http\MiddlewarePipeline;
use Core\Http\Request;
use Core\Http\Response;
use Core\Http\Router;
use Core\I18n\Translator;
use Core\Middleware\AuthGuard;
use Core\Middleware\CompanyContext;
use Core\Middleware\CorsHandler;
use Core\Middleware\Csrf;
use Core\Middleware\ErrorHandler;
use Core\Middleware\JsonBody;
use Core\Middleware\PermissionGuard;
use Core\Module\ModuleRegistry;
use Core\Module\ModuleScanner;
use Core\Stubs\NullAudit;
use Core\Stubs\NullAuth;
use Core\Stubs\NullCompany;
use Core\Stubs\NullDynamicField;
use Core\Stubs\NullNotifier;
use Core\Stubs\NullPermission;
use Core\Stubs\NullPrint;
use Core\Stubs\NullWorkflow;
use Core\Support\FileCache;
use Core\Support\Logger;
use Core\Support\SettingsService;

final class Kernel
{
    public readonly Container $container;
    public readonly Config $config;
    public readonly Router $router;
    public readonly ModuleRegistry $registry;

    public function __construct(private readonly string $root)
    {
        Env::load($this->root . '/.env');
        date_default_timezone_set(Env::get('TIMEZONE', 'Asia/Tehran') ?? 'Asia/Tehran');

        $this->config = new Config([
            'app' => [
                'env' => Env::get('APP_ENV', 'local'),
                'key' => Env::get('APP_KEY', ''),
                'url' => Env::get('APP_URL', 'http://localhost'),
                'locale' => Env::get('DEFAULT_LOCALE', 'fa'),
            ],
            'db' => [
                'connection' => Env::get('DB_CONNECTION', 'mysql'),
                'host' => Env::get('DB_HOST', '127.0.0.1'),
                'port' => Env::int('DB_PORT', 3306),
                'database' => Env::get('DB_DATABASE', 'erp'),
                'username' => Env::get('DB_USERNAME', 'root'),
                'password' => Env::get('DB_PASSWORD', ''),
            ],
            'ai' => [
                'enabled' => false,
                'kill_switch' => Env::bool('AI_KILL_SWITCH', false),
                'avalai_api_key' => Env::get('AVALAI_API_KEY', ''),
                'avalai_base_url' => Env::get('AVALAI_BASE_URL', 'https://api.avalai.ir/v1'),
                'service_url' => Env::get('AI_SERVICE_URL', 'http://127.0.0.1:8000'),
            ],
        ]);

        $this->container = new Container();
        $this->container->singleton(Config::class, $this->config);
        $this->container->singleton(Translator::class, new Translator($this->root . '/core/lang', $this->config->string('app.locale', 'fa')));
        $this->container->singleton(FileCache::class, new FileCache($this->root . '/storage/cache'));
        $this->container->singleton(Logger::class, new Logger($this->root . '/storage/logs'));

        // Default null/stub contracts
        $this->container->singleton(AuthContract::class, NullAuth::class);
        $this->container->singleton(PermissionContract::class, NullPermission::class);
        $this->container->singleton(CompanyContract::class, NullCompany::class);
        $this->container->singleton(AuditContract::class, NullAudit::class);
        $this->container->singleton(NotifierContract::class, NullNotifier::class);
        $this->container->singleton(WorkflowContract::class, NullWorkflow::class);
        $this->container->singleton(DynamicFieldContract::class, NullDynamicField::class);
        $this->container->singleton(PrintContract::class, NullPrint::class);

        $scanner = new ModuleScanner($this->root);
        $allModules = $scanner->scan();
        $modulesCfg = is_file($this->root . '/config/modules.php') ? (array) require $this->root . '/config/modules.php' : [];
        $enabledIds = (array) ($modulesCfg['enabled'] ?? []);
        $this->registry = new ModuleRegistry($allModules, $enabledIds);
        $this->container->singleton(ModuleRegistry::class, $this->registry);

        $this->router = new Router();
        $this->container->singleton(Router::class, $this->router);
        $this->container->singleton(EventBus::class, fn() => new EventBus(null));
        $this->container->singleton(AiGatewayContract::class, AiGateway::class);
        $this->container->singleton(SettingsService::class, fn() => new SettingsService($this->registry, null, $this->container->make(FileCache::class)));

        $this->registerCoreRoutes();
        $this->registerEnabledModules();
    }

    public function handle(Request $request): Response
    {
        $debug = $this->config->string('app.env', 'local') !== 'production';
        $pipeline = (new MiddlewarePipeline())
            ->pipe(new ErrorHandler($debug))
            ->pipe(new CorsHandler())
            ->pipe(new JsonBody())
            ->pipe(new Csrf());

        return $pipeline->handle($request, function (Request $req): Response {
            $route = $this->router->match($req->method, $req->path);
            if ($route === null) {
                return Response::error('NOT_FOUND', 'Route not found', 404);
            }
            $req->params = $route['params'] ?? [];

            $routePipeline = new MiddlewarePipeline();
            if ($req->path !== '/api/_health') {
                $routePipeline->pipe(new AuthGuard($this->container->make(AuthContract::class)));
                $routePipeline->pipe(new CompanyContext($this->container->make(CompanyContract::class)));
                if (!empty($route['permission'])) {
                    $routePipeline->pipe(new PermissionGuard(
                        $this->container->make(PermissionContract::class),
                        (string) $route['permission']
                    ));
                }
            }

            return $routePipeline->handle($req, function (Request $r) use ($route): Response {
                $handler = $route['handler'];
                if (is_callable($handler)) {
                    $res = $handler($r, $this->container);
                    return $res instanceof Response ? $res : Response::ok($res);
                }
                return Response::error('INVALID_HANDLER', 'Invalid route handler', 500);
            });
        });
    }

    private function registerCoreRoutes(): void
    {
        $this->router->get('/api/_health', fn(): Response => Response::ok([
            'status' => 'healthy',
            'time' => gmdate('Y-m-d\TH:i:s\Z'),
        ]));

        $this->router->get('/api/_meta/modules', fn(): Response => Response::ok([
            'modules' => array_values($this->registry->enabled()),
            'menus' => $this->registry->menus(),
            'permissions' => $this->registry->permissions(),
        ]));

        $this->router->get('/api/_meta/settings', function (Request $req): Response {
            $companyId = (int) ($req->attributes['company_id'] ?? 1);
            $svc = $this->container->make(SettingsService::class);
            $out = [];
            foreach ($this->registry->enabled() as $id => $manifest) {
                foreach ($manifest['settings'] ?? [] as $s) {
                    $k = (string) ($s['key'] ?? '');
                    if ($k !== '') {
                        $out[$id][$k] = $svc->get($companyId, $id, $k);
                    }
                }
            }
            return Response::ok($out);
        });
    }

    private function registerEnabledModules(): void
    {
        foreach ($this->registry->enabled() as $id => $manifest) {
            $routesFile = $this->root . "/modules/$id/backend/routes.php";
            if (is_file($routesFile)) {
                $registrar = require $routesFile;
                if (is_callable($registrar)) {
                    $this->router->group("/api/$id", function (Router $r) use ($registrar): void {
                        $registrar($r, $this->container);
                    });
                }
            }
            $listenersFile = $this->root . "/modules/$id/backend/listeners.php";
            if (is_file($listenersFile)) {
                $bus = $this->container->make(EventBus::class);
                $registrar = require $listenersFile;
                if (is_callable($registrar)) {
                    $registrar($bus, $this->container);
                }
            }
        }
    }
}
