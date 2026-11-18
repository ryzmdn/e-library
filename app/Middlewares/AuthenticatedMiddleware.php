<?php
declare(strict_types=1);

namespace App\Middlewares;

use App\Core\MiddlewareInterface;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;

final class AuthenticatedMiddleware implements MiddlewareInterface
{
    public function handle(Request $request, callable $next): Response
    {
        if (!Session::isAuthenticated()) {
            if ($request->isJson()) {
                return Response::json(['error' => 'Akses ditolak. Silakan login terlebih dahulu.'], 401);
            }
            Session::setFlash('error', 'Silakan masuk ke akun Anda untuk mengakses halaman ini.');
            return Response::redirect('/login');
        }

        return $next($request);
    }
}
