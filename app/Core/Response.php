<?php
declare(strict_types=1);

namespace App\Core;

final class Response
{
    private int $statusCode;
    private array $headers;
    private string $content;

    public function __construct(string $content = '', int $statusCode = 200, array $headers = [])
    {
        $this->content = $content;
        $this->statusCode = $statusCode;
        $this->headers = $headers;
    }

    public static function html(string $html, int $statusCode = 200, array $headers = []): self
    {
        $defaultHeaders = ['Content-Type' => 'text/html; charset=UTF-8'];
        return new self($html, $statusCode, array_merge($defaultHeaders, $headers));
    }

    public static function json(mixed $data, int $statusCode = 200, array $headers = []): self
    {
        $defaultHeaders = ['Content-Type' => 'application/json; charset=UTF-8'];
        $encoded = json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        return new self($encoded !== false ? $encoded : '{}', $statusCode, array_merge($defaultHeaders, $headers));
    }

    public static function redirect(string $url, int $statusCode = 302): self
    {
        return new self('', $statusCode, ['Location' => $url]);
    }

    public function setStatusCode(int $statusCode): self
    {
        $this->statusCode = $statusCode;
        return $this;
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public function setHeader(string $name, string $value): self
    {
        $this->headers[$name] = $value;
        return $this;
    }

    public function getHeaders(): array
    {
        return $this->headers;
    }

    public function getHeader(string $name): ?string
    {
        $lower = strtolower($name);
        foreach ($this->headers as $key => $value) {
            if (strtolower((string)$key) === $lower) {
                return (string)$value;
            }
        }
        return null;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;
        return $this;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function send(): void
    {
        if (!headers_sent()) {
            http_response_code($this->statusCode);
            foreach ($this->headers as $name => $value) {
                header("{$name}: {$value}");
            }
        }

        echo $this->content;
    }
}
