<?php
declare(strict_types=1);

namespace App\Core;

use App\Config\Environment;
use RuntimeException;

abstract class Controller
{
    protected function render(string $view, array $data = [], string $layout = 'main'): Response
    {
        $baseViewsDir = dirname(__DIR__) . '/Views/';
        $viewFile = $baseViewsDir . str_replace('.', '/', $view) . '.php';

        if (!file_exists($viewFile)) {
            throw new RuntimeException("Berkas tampilan tidak ditemukan: {$viewFile}");
        }

        $globalData = [
            'appName' => Environment::get('APP_NAME', 'E-Library'),
            'appUrl' => Environment::get('APP_URL', ''),
            'csrfToken' => Session::getCsrfToken(),
            'isAuthenticated' => Session::isAuthenticated(),
            'currentUser' => Session::getUser(),
            'currentUserRole' => Session::getUserRole(),
            'currentUserName' => Session::getUser()['full_name'] ?? 'Pengguna',
            'flashSuccess' => Session::getFlash('success'),
            'flashError' => Session::getFlash('error'),
            'flashInfo' => Session::getFlash('info'),
        ];

        $viewData = array_merge($globalData, $data);

        extract($viewData, EXTR_SKIP);

        ob_start();
        require $viewFile;
        $content = ob_get_clean();

        if ($layout === '') {
            return Response::html((string)$content);
        }

        $layoutFile = $baseViewsDir . 'layouts/' . $layout . '.php';
        if (!file_exists($layoutFile)) {
            throw new RuntimeException("Berkas tata letak tidak ditemukan: {$layoutFile}");
        }

        ob_start();
        require $layoutFile;
        $fullHtml = ob_get_clean();

        return Response::html((string)$fullHtml);
    }

    protected function json(mixed $data, int $statusCode = 200): Response
    {
        return Response::json($data, $statusCode);
    }

    protected function redirect(string $url, int $statusCode = 302): Response
    {
        return Response::redirect($url, $statusCode);
    }
}
