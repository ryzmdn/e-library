<?php
declare(strict_types=1);

namespace App\Core;

use App\Config\Environment;
use Throwable;

final class Application
{
    private string $basePath;
    private Router $router;

    public function __construct(string $basePath)
    {
        $this->basePath = rtrim($basePath, '/\\');
        $this->bootstrap();
        $this->router = new Router();
    }

    private function bootstrap(): void
    {
        Environment::load($this->basePath . '/.env');

        $debug = Environment::getBool('APP_DEBUG', false);
        if ($debug) {
            error_reporting(E_ALL);
            ini_set('display_errors', '1');
            ini_set('display_startup_errors', '1');
        } else {
            error_reporting(0);
            ini_set('display_errors', '0');
            ini_set('display_startup_errors', '0');
            ini_set('log_errors', '1');
            $logDir = $this->basePath . '/storage/logs';
            if (is_dir($logDir) && is_writable($logDir)) {
                ini_set('error_log', $logDir . '/app-error.log');
            }
        }

        Session::start();
    }

    public function getRouter(): Router
    {
        return $this->router;
    }

    public function run(): void
    {
        $request = new Request();

        try {
            $response = $this->router->dispatch($request);
            $response->send();
        } catch (Throwable $e) {
            $this->handleException($e, $request);
        }
    }

    private function handleException(Throwable $e, Request $request): void
    {
        $debug = Environment::getBool('APP_DEBUG', false);
        $referenceId = bin2hex(random_bytes(6));

        $logMessage = sprintf(
            "[%s] [%s] %s in %s:%d\nStack trace:\n%s\n",
            date('Y-m-d H:i:s'),
            $referenceId,
            $e->getMessage(),
            $e->getFile(),
            $e->getLine(),
            $e->getTraceAsString()
        );

        $logPath = $this->basePath . '/storage/logs/app-error.log';
        if (is_dir(dirname($logPath))) {
            file_put_contents($logPath, $logMessage, FILE_APPEND);
        }

        if ($request->isJson()) {
            $payload = [
                'error' => 'Terjadi kesalahan sistem internal.',
                'reference_id' => $referenceId,
            ];
            if ($debug) {
                $payload['details'] = [
                    'message' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                ];
            }
            Response::json($payload, 500)->send();
            return;
        }

        if ($debug) {
            $html = '<!DOCTYPE html><html lang="id"><head><meta charset="UTF-8"><title>Galat Sistem</title><style>body{font-family:sans-serif;padding:2rem;line-height:1.5;background:#fef2f2;color:#991b1b;}pre{background:#fff;padding:1rem;border:1px solid #fecaca;border-radius:6px;overflow-x:auto;color:#18181b;}</style></head><body>';
            $html .= '<h1>Galat Fatal Terjadi (Mode Pengembang)</h1>';
            $html .= '<p><strong>Pesan:</strong> ' . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8') . '</p>';
            $html .= '<p><strong>Lokasi:</strong> ' . htmlspecialchars($e->getFile(), ENT_QUOTES, 'UTF-8') . ':' . $e->getLine() . '</p>';
            $html .= '<p><strong>ID Referensi:</strong> ' . $referenceId . '</p>';
            $html .= '<h2>Jejak Tumpukan</h2><pre>' . htmlspecialchars($e->getTraceAsString(), ENT_QUOTES, 'UTF-8') . '</pre>';
            $html .= '</body></html>';
            Response::html($html, 500)->send();
            return;
        }

        $html = '<!DOCTYPE html><html lang="id"><head><meta charset="UTF-8"><title>500 Kesalahan Server</title><style>body{font-family:sans-serif;display:flex;align-items:center;justify-content:center;height:100vh;margin:0;background:#f8fafc;color:#0f172a;}div{max-width:480px;text-align:center;padding:2rem;background:#fff;border:1px solid #e2e8f0;border-radius:8px;}h1{margin-top:0;font-size:1.5rem;}p{color:#64748b;font-size:0.9rem;}</style></head><body>';
        $html .= '<div><h1>500 Kesalahan Server Internal</h1><p>Mohon maaf, sistem sedang mengalami kendala pemrosesan. Silakan muat ulang halaman beberapa saat lagi.</p><p><small>Kode Referensi: ' . $referenceId . '</small></p></div></body></html>';
        Response::html($html, 500)->send();
    }
}
