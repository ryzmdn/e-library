<?php
declare(strict_types=1);

namespace App\Middlewares;

use App\Core\MiddlewareInterface;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;

final class RoleAuthorizeMiddleware implements MiddlewareInterface
{
    private array $allowedRoles;

    public function __construct(string ...$allowedRoles)
    {
        $this->allowedRoles = $allowedRoles;
    }

    public function handle(Request $request, callable $next): Response
    {
        if (!Session::isAuthenticated()) {
            if ($request->isJson()) {
                return Response::json(['error' => 'Akses ditolak. Silakan login terlebih dahulu.'], 401);
            }
            Session::setFlash('error', 'Silakan masuk untuk melanjutkan.');
            return Response::redirect('/login');
        }

        $userRole = Session::getUserRole();
        if (!in_array($userRole, $this->allowedRoles, true)) {
            if ($request->isJson()) {
                return Response::json(['error' => 'Anda tidak memiliki hak akses untuk tindakan ini.'], 403);
            }
            return Response::html('<h1>403 Akses Dilarang</h1><p>Akun Anda tidak memiliki wewenang untuk membuka halaman ini.</p>', 403);
        }

        return $next($request);
    }
}
