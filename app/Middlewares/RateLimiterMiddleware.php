<?php
declare(strict_types=1);

namespace App\Middlewares;

use App\Core\MiddlewareInterface;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;

final class RateLimiterMiddleware implements MiddlewareInterface
{
    private int $maxAttempts;
    private int $decaySeconds;

    public function __construct(int $maxAttempts = 60, int $decaySeconds = 60)
    {
        $this->maxAttempts = $maxAttempts;
        $this->decaySeconds = $decaySeconds;
    }

    public function handle(Request $request, callable $next): Response
    {
        Session::start();
        $ip = $request->getIp();
        $key = '_rate_limit_' . md5($ip . $request->getPath());
        $currentTime = time();

        $record = Session::get($key, ['attempts' => 0, 'expires_at' => $currentTime + $this->decaySeconds]);

        if ($currentTime > $record['expires_at']) {
            $record = ['attempts' => 1, 'expires_at' => $currentTime + $this->decaySeconds];
        } else {
            $record['attempts']++;
        }

        Session::set($key, $record);

        if ($record['attempts'] > $this->maxAttempts) {
            $retryAfter = $record['expires_at'] - $currentTime;
            if ($request->isJson()) {
                return Response::json([
                    'error' => 'Terlalu banyak permintaan. Silakan tunggu beberapa saat.',
                    'retry_after' => $retryAfter,
                ], 429, ['Retry-After' => (string)$retryAfter]);
            }
            return Response::html('<h1>429 Terlalu Banyak Permintaan</h1><p>Batas akses tercapai. Harap tunggu sebentar sebelum mencoba kembali.</p>', 429, [
                'Retry-After' => (string)$retryAfter
            ]);
        }

        return $next($request);
    }
}
