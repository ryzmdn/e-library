<?php
declare(strict_types=1);

namespace App\Core;

final class Router
{
    private array $routes = [];
    private string $currentGroupPrefix = '';
    private array $currentGroupMiddlewares = [];

    public function get(string $path, callable|array $handler, array $middlewares = []): self
    {
        $this->addRoute('GET', $path, $handler, $middlewares);
        return $this;
    }

    public function post(string $path, callable|array $handler, array $middlewares = []): self
    {
        $this->addRoute('POST', $path, $handler, $middlewares);
        return $this;
    }

    public function put(string $path, callable|array $handler, array $middlewares = []): self
    {
        $this->addRoute('PUT', $path, $handler, $middlewares);
        return $this;
    }

    public function patch(string $path, callable|array $handler, array $middlewares = []): self
    {
        $this->addRoute('PATCH', $path, $handler, $middlewares);
        return $this;
    }

    public function delete(string $path, callable|array $handler, array $middlewares = []): self
    {
        $this->addRoute('DELETE', $path, $handler, $middlewares);
        return $this;
    }

    public function group(string $prefix, callable $callback, array $middlewares = []): void
    {
        $previousPrefix = $this->currentGroupPrefix;
        $previousMiddlewares = $this->currentGroupMiddlewares;

        $this->currentGroupPrefix = rtrim($previousPrefix, '/') . '/' . trim($prefix, '/');
        $this->currentGroupMiddlewares = array_merge($previousMiddlewares, $middlewares);

        $callback($this);

        $this->currentGroupPrefix = $previousPrefix;
        $this->currentGroupMiddlewares = $previousMiddlewares;
    }

    private function addRoute(string $method, string $path, callable|array $handler, array $middlewares): void
    {
        $fullPath = rtrim($this->currentGroupPrefix, '/') . '/' . trim($path, '/');
        if ($fullPath === '') {
            $fullPath = '/';
        }
        $fullPath = '/' . trim($fullPath, '/');

        $combinedMiddlewares = array_merge($this->currentGroupMiddlewares, $middlewares);

        $this->routes[] = [
            'method' => strtoupper($method),
            'path' => $fullPath,
            'pattern' => $this->convertPathToRegex($fullPath),
            'handler' => $handler,
            'middlewares' => $combinedMiddlewares,
        ];
    }

    private function convertPathToRegex(string $path): string
    {
        $pattern = preg_replace('/\{([a-zA-Z0-9_]+)\}/', '(?P<$1>[^/]+)', $path);
        return '#^' . $pattern . '$#';
    }

    public function dispatch(Request $request): Response
    {
        $method = $request->getMethod();
        $path = $request->getPath();

        $allowedMethodsForPath = [];

        foreach ($this->routes as $route) {
            if (preg_match($route['pattern'], $path, $matches)) {
                if ($route['method'] === $method) {
                    $parameters = [];
                    foreach ($matches as $key => $value) {
                        if (is_string($key)) {
                            $parameters[$key] = $value;
                        }
                    }

                    return $this->runPipeline($request, $route['middlewares'], function (Request $req) use ($route, $parameters): Response {
                        return $this->invokeHandler($route['handler'], $req, $parameters);
                    });
                }
                $allowedMethodsForPath[] = $route['method'];
            }
        }

        if (!empty($allowedMethodsForPath)) {
            $allowedString = implode(', ', array_unique($allowedMethodsForPath));
            if ($request->isJson()) {
                return Response::json(['error' => 'Metode HTTP tidak diizinkan.', 'allowed' => $allowedString], 405, ['Allow' => $allowedString]);
            }
            return Response::html('<h1>405 Metode Tidak Diizinkan</h1><p>Metode HTTP yang diminta tidak didukung untuk halaman ini.</p>', 405, ['Allow' => $allowedString]);
        }

        if ($request->isJson()) {
            return Response::json(['error' => 'Rute tidak ditemukan.'], 404);
        }

        return Response::html('<h1>404 Halaman Tidak Ditemukan</h1><p>Halaman atau sumber daya yang Anda cari tidak tersedia.</p>', 404);
    }

    private function runPipeline(Request $request, array $middlewares, callable $destination): Response
    {
        $pipeline = array_reduce(
            array_reverse($middlewares),
            function (callable $next, mixed $middlewareClass): callable {
                return function (Request $req) use ($middlewareClass, $next): Response {
                    if (is_string($middlewareClass)) {
                        $instance = new $middlewareClass();
                    } else {
                        $instance = $middlewareClass;
                    }

                    if ($instance instanceof MiddlewareInterface) {
                        return $instance->handle($req, $next);
                    }

                    return $next($req);
                };
            },
            $destination
        );

        return $pipeline($request);
    }

    private function invokeHandler(callable|array $handler, Request $request, array $parameters): Response
    {
        if (is_array($handler)) {
            [$class, $method] = $handler;
            $controller = new $class();
            $result = $controller->$method($request, ...array_values($parameters));
        } else {
            $result = $handler($request, ...array_values($parameters));
        }

        if ($result instanceof Response) {
            return $result;
        }

        if (is_array($result)) {
            return Response::json($result);
        }

        return Response::html((string)$result);
    }
}
