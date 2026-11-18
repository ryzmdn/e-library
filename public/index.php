<?php
declare(strict_types=1);

spl_autoload_register(function (string $class): void {
    $prefix = 'App\\';
    $baseDir = dirname(__DIR__) . '/app/';

    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }

    $relativeClass = substr($class, $len);
    $file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';

    if (file_exists($file)) {
        require $file;
    }
});

use App\Controllers\Member\DashboardMemberController;
use App\Controllers\Public\AuthController;
use App\Controllers\Public\CatalogPublicController;
use App\Controllers\Public\HomeController;
use App\Core\Application;
use App\Middlewares\AuthenticatedMiddleware;
use App\Middlewares\CsrfVerifyMiddleware;
use App\Middlewares\GuestOnlyMiddleware;

$app = new Application(dirname(__DIR__));
$router = $app->getRouter();

$router->get('/', [HomeController::class, 'index']);

$router->get('/catalog', [CatalogPublicController::class, 'index']);
$router->get('/catalog/{slug}', [CatalogPublicController::class, 'show']);
$router->get('/book/{slug}', [CatalogPublicController::class, 'show']);
$router->get('/api/catalog/suggest', [CatalogPublicController::class, 'searchSuggest']);

$router->get('/login', [AuthController::class, 'showLoginForm'], [GuestOnlyMiddleware::class]);
$router->post('/login', [AuthController::class, 'login'], [CsrfVerifyMiddleware::class, GuestOnlyMiddleware::class]);

$router->get('/register', [AuthController::class, 'showRegisterForm'], [GuestOnlyMiddleware::class]);
$router->post('/register', [AuthController::class, 'register'], [CsrfVerifyMiddleware::class, GuestOnlyMiddleware::class]);

$router->post('/logout', [AuthController::class, 'logout'], [CsrfVerifyMiddleware::class]);

$router->group('/member', function ($r): void {
    $r->get('/dashboard', [DashboardMemberController::class, 'index']);
    $r->get('/profile', [DashboardMemberController::class, 'profile']);
    $r->post('/profile', [DashboardMemberController::class, 'updateProfile'], [CsrfVerifyMiddleware::class]);
}, [AuthenticatedMiddleware::class]);

$app->run();
