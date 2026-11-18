<?php
declare(strict_types=1);

namespace App\Middlewares;

use App\Core\MiddlewareInterface;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;

final class CsrfVerifyMiddleware implements MiddlewareInterface
{
    public function handle(Request $request, callable $next): Response
    {
        $method = $request->getMethod();
        if (in_array($method, ['POST', 'PUT', 'PATCH', 'DELETE'], true)) {
            $token = $request->input('_csrf_token') ?? $request->input('csrf_token') ?? $request->getHeader('x-csrf-token');
            if (!Session::verifyCsrfToken($token !== null ? (string)$token : null)) {
                if ($request->isJson()) {
                    return Response::json(['error' => 'Token CSRF tidak valid atau sesi telah kedaluwarsa.'], 419);
                }
                return Response::html('<h1>419 Sesi Kedaluwarsa</h1><p>Sesi keamanan formulir Anda telah berakhir. Silakan muat ulang halaman dan coba kembali.</p>', 419);
            }
        }

        return $next($request);
    }
}
