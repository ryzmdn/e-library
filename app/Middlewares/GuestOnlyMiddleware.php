<?php
declare(strict_types=1);

namespace App\Middlewares;

use App\Core\MiddlewareInterface;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;

final class GuestOnlyMiddleware implements MiddlewareInterface
{
    public function handle(Request $request, callable $next): Response
    {
        if (Session::isAuthenticated()) {
            $role = Session::getUserRole();
            if ($role === 'SUPER_ADMIN' || $role === 'LIBRARIAN' || $role === 'CATALOGER') {
                return Response::redirect('/admin/dashboard');
            }
            return Response::redirect('/member/dashboard');
        }

        return $next($request);
    }
}
