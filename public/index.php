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

use App\Controllers\Admin\BookCopyAdminController;
use App\Controllers\Admin\CatalogAdminController;
use App\Controllers\Admin\CirculationAdminController;
use App\Controllers\Admin\DashboardAdminController;
use App\Controllers\Admin\FineAdminController;
use App\Controllers\Admin\MemberAdminController;
use App\Controllers\Admin\TaskAdminController;
use App\Controllers\Member\DashboardMemberController;
use App\Controllers\Member\DigitalReaderController;
use App\Controllers\Member\FavoriteMemberController;
use App\Controllers\Member\LoanMemberController;
use App\Controllers\Member\ReadingHistoryMemberController;
use App\Controllers\Member\ReservationMemberController;
use App\Controllers\Public\AuthController;
use App\Controllers\Public\CatalogPublicController;
use App\Controllers\Public\HomeController;
use App\Core\Application;
use App\Middlewares\AuthenticatedMiddleware;
use App\Middlewares\CsrfVerifyMiddleware;
use App\Middlewares\GuestOnlyMiddleware;
use App\Middlewares\RoleAuthorizeMiddleware;

$app = new Application(dirname(__DIR__));
$router = $app->getRouter();

$router->get('/', [HomeController::class, 'index']);

$router->get('/catalog', [CatalogPublicController::class, 'index']);
$router->get('/catalog/{slug}', [CatalogPublicController::class, 'show']);
$router->get('/book/{slug}', [CatalogPublicController::class, 'show']);
$router->get('/api/catalog/suggest', [CatalogPublicController::class, 'searchSuggest']);
$router->get('/favorites', [FavoriteMemberController::class, 'index']);
$router->get('/history', [ReadingHistoryMemberController::class, 'index']);
$router->post('/api/favorites/toggle', [FavoriteMemberController::class, 'toggle']);

$router->get('/reader/{slug}', [DigitalReaderController::class, 'open'], [AuthenticatedMiddleware::class]);
$router->get('/reader/stream/{assetId}', [DigitalReaderController::class, 'stream'], [AuthenticatedMiddleware::class]);
$router->post('/api/reader/progress', [DigitalReaderController::class, 'saveProgress'], [AuthenticatedMiddleware::class]);

$router->get('/login', [AuthController::class, 'showLoginForm'], [GuestOnlyMiddleware::class]);
$router->post('/login', [AuthController::class, 'login'], [CsrfVerifyMiddleware::class, GuestOnlyMiddleware::class]);

$router->get('/register', [AuthController::class, 'showRegisterForm'], [GuestOnlyMiddleware::class]);
$router->post('/register', [AuthController::class, 'register'], [CsrfVerifyMiddleware::class, GuestOnlyMiddleware::class]);

$router->post('/logout', [AuthController::class, 'logout'], [CsrfVerifyMiddleware::class]);

$router->group('/member', function ($r): void {
    $r->get('/dashboard', [DashboardMemberController::class, 'index']);
    $r->get('/profile', [DashboardMemberController::class, 'profile']);
    $r->post('/profile', [DashboardMemberController::class, 'updateProfile'], [CsrfVerifyMiddleware::class]);

    $r->get('/loans', [LoanMemberController::class, 'index']);
    $r->post('/loans/{id}/renew', [LoanMemberController::class, 'renew'], [CsrfVerifyMiddleware::class]);

    $r->get('/reservations', [ReservationMemberController::class, 'index']);
    $r->post('/reservations', [ReservationMemberController::class, 'create'], [CsrfVerifyMiddleware::class]);
    $r->post('/reservations/{id}/cancel', [ReservationMemberController::class, 'cancel'], [CsrfVerifyMiddleware::class]);

    $r->get('/favorites', [FavoriteMemberController::class, 'index']);
    $r->post('/favorites/note', [FavoriteMemberController::class, 'updateNote'], [CsrfVerifyMiddleware::class]);
    $r->post('/favorites/{id}/remove', [FavoriteMemberController::class, 'remove'], [CsrfVerifyMiddleware::class]);

    $r->get('/history', [ReadingHistoryMemberController::class, 'index']);
}, [AuthenticatedMiddleware::class]);

$staffAuth = new RoleAuthorizeMiddleware('SUPER_ADMIN', 'LIBRARIAN', 'CATALOGER');

$router->group('/admin', function ($r): void {
    $r->get('', [DashboardAdminController::class, 'index']);
    $r->get('/dashboard', [DashboardAdminController::class, 'index']);

    $r->get('/catalog', [CatalogAdminController::class, 'index']);
    $r->get('/catalog/create', [CatalogAdminController::class, 'create']);
    $r->post('/catalog/create', [CatalogAdminController::class, 'create'], [CsrfVerifyMiddleware::class]);
    $r->get('/catalog/{id}/edit', [CatalogAdminController::class, 'edit']);
    $r->post('/catalog/{id}/edit', [CatalogAdminController::class, 'edit'], [CsrfVerifyMiddleware::class]);
    $r->post('/catalog/{id}/delete', [CatalogAdminController::class, 'delete'], [CsrfVerifyMiddleware::class]);

    $r->get('/copies', [BookCopyAdminController::class, 'index']);
    $r->get('/copies/create', [BookCopyAdminController::class, 'create']);
    $r->post('/copies/create', [BookCopyAdminController::class, 'create'], [CsrfVerifyMiddleware::class]);
    $r->get('/copies/{id}/edit', [BookCopyAdminController::class, 'edit']);
    $r->post('/copies/{id}/edit', [BookCopyAdminController::class, 'edit'], [CsrfVerifyMiddleware::class]);
    $r->post('/copies/{id}/delete', [BookCopyAdminController::class, 'delete'], [CsrfVerifyMiddleware::class]);
    $r->get('/copies/{id}/label', [BookCopyAdminController::class, 'label']);

    $r->get('/circulation', [CirculationAdminController::class, 'index']);
    $r->post('/circulation/checkout', [CirculationAdminController::class, 'checkout'], [CsrfVerifyMiddleware::class]);
    $r->post('/circulation/checkin', [CirculationAdminController::class, 'checkin'], [CsrfVerifyMiddleware::class]);
    $r->get('/circulation/history', [CirculationAdminController::class, 'history']);

    $r->get('/members', [MemberAdminController::class, 'index']);
    $r->get('/members/{id}', [MemberAdminController::class, 'detail']);
    $r->post('/members/{id}/status', [MemberAdminController::class, 'updateStatus'], [CsrfVerifyMiddleware::class]);
    $r->post('/members/{id}/tier', [MemberAdminController::class, 'updateTier'], [CsrfVerifyMiddleware::class]);
    $r->post('/members/{id}/extend', [MemberAdminController::class, 'extendMembership'], [CsrfVerifyMiddleware::class]);

    $r->get('/fines', [FineAdminController::class, 'index']);
    $r->post('/fines/pay', [FineAdminController::class, 'pay'], [CsrfVerifyMiddleware::class]);

    $r->get('/tasks', [TaskAdminController::class, 'index']);
    $r->post('/tasks/fines', [TaskAdminController::class, 'runFines'], [CsrfVerifyMiddleware::class]);
    $r->post('/tasks/reservations', [TaskAdminController::class, 'runReservations'], [CsrfVerifyMiddleware::class]);
    $r->post('/tasks/run-all', [TaskAdminController::class, 'runAll'], [CsrfVerifyMiddleware::class]);
}, [AuthenticatedMiddleware::class, $staffAuth]);

$app->run();
