<?php
declare(strict_types=1);

namespace App\Core;

final class Request
{
    private array $getParams;
    private array $postParams;
    private array $serverParams;
    private array $files;
    private array $headers;
    private ?array $jsonBody = null;

    public function __construct(
        ?array $get = null,
        ?array $post = null,
        ?array $server = null,
        ?array $files = null
    ) {
        $this->getParams = $get ?? $_GET;
        $this->postParams = $post ?? $_POST;
        $this->serverParams = $server ?? $_SERVER;
        $this->files = $files ?? $_FILES;
        $this->headers = $this->extractHeaders();
    }

    private function extractHeaders(): array
    {
        $headers = [];
        foreach ($this->serverParams as $key => $value) {
            if (str_starts_with($key, 'HTTP_')) {
                $name = str_replace('_', '-', strtolower(substr($key, 5)));
                $headers[$name] = (string)$value;
            } elseif (in_array($key, ['CONTENT_TYPE', 'CONTENT_LENGTH'], true)) {
                $name = str_replace('_', '-', strtolower($key));
                $headers[$name] = (string)$value;
            }
        }
        return $headers;
    }

    public function getMethod(): string
    {
        $method = strtoupper($this->serverParams['REQUEST_METHOD'] ?? 'GET');
        if ($method === 'POST') {
            $override = $this->postParams['_method'] ?? $this->getHeader('x-http-method-override');
            if ($override !== null) {
                $override = strtoupper((string)$override);
                if (in_array($override, ['PUT', 'PATCH', 'DELETE'], true)) {
                    return $override;
                }
            }
        }
        return $method;
    }

    public function getPath(): string
    {
        $uri = $this->serverParams['REQUEST_URI'] ?? '/';
        $position = strpos($uri, '?');
        if ($position !== false) {
            $uri = substr($uri, 0, $position);
        }

        $scriptName = $this->serverParams['SCRIPT_NAME'] ?? '';
        $baseDir = dirname($scriptName);
        if ($baseDir !== '/' && $baseDir !== '\\' && str_starts_with($uri, $baseDir)) {
            $uri = substr($uri, strlen($baseDir));
        }

        $trimmed = trim($uri, '/');
        return $trimmed === '' ? '/' : '/' . $trimmed;
    }

    public function getQuery(?string $key = null, mixed $default = null): mixed
    {
        if ($key === null) {
            return $this->getParams;
        }
        return $this->getParams[$key] ?? $default;
    }

    public function getPost(?string $key = null, mixed $default = null): mixed
    {
        if ($key === null) {
            return $this->postParams;
        }
        return $this->postParams[$key] ?? $default;
    }

    public function input(?string $key = null, mixed $default = null): mixed
    {
        $all = $this->all();
        if ($key === null) {
            return $all;
        }
        return $all[$key] ?? $default;
    }

    public function all(): array
    {
        $json = $this->getJsonBody();
        if ($json !== null) {
            return array_merge($this->getParams, $json);
        }
        return array_merge($this->getParams, $this->postParams);
    }

    public function getHeader(string $name, ?string $default = null): ?string
    {
        $normalized = strtolower($name);
        return $this->headers[$normalized] ?? $default;
    }

    public function isJson(): bool
    {
        $contentType = $this->getHeader('content-type', '');
        return str_contains($contentType, 'application/json');
    }

    public function getJsonBody(): ?array
    {
        if ($this->jsonBody !== null) {
            return $this->jsonBody;
        }

        if ($this->isJson()) {
            $raw = file_get_contents('php://input');
            if ($raw !== false && trim($raw) !== '') {
                $decoded = json_decode($raw, true);
                if (is_array($decoded)) {
                    $this->jsonBody = $decoded;
                    return $this->jsonBody;
                }
            }
        }

        return null;
    }

    public function getFiles(): array
    {
        return $this->files;
    }

    public function getFile(string $key): ?array
    {
        return $this->files[$key] ?? null;
    }

    public function getIp(): string
    {
        $forwarded = $this->getHeader('x-forwarded-for');
        if ($forwarded !== null) {
            $parts = explode(',', $forwarded);
            return trim($parts[0]);
        }
        return (string)($this->serverParams['REMOTE_ADDR'] ?? '127.0.0.1');
    }

    public function getUserAgent(): string
    {
        return (string)($this->serverParams['HTTP_USER_AGENT'] ?? 'Unknown');
    }
}
