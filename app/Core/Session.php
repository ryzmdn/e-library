<?php
declare(strict_types=1);

namespace App\Core;

use App\Config\Environment;

final class Session
{
    private static bool $started = false;

    public static function start(): void
    {
        if (self::$started || session_status() === PHP_SESSION_ACTIVE) {
            self::$started = true;
            return;
        }

        $lifetime = Environment::getInt('SESSION_LIFETIME', 7200);
        $secure = Environment::getBool('SESSION_SECURE_COOKIE', false);

        ini_set('session.use_strict_mode', '1');
        ini_set('session.use_only_cookies', '1');

        session_set_cookie_params([
            'lifetime' => $lifetime,
            'path' => '/',
            'domain' => '',
            'secure' => $secure,
            'httponly' => true,
            'samesite' => 'Lax',
        ]);

        session_start();
        self::$started = true;

        if (!isset($_SESSION['_created_at'])) {
            $_SESSION['_created_at'] = time();
        } elseif (time() - $_SESSION['_created_at'] > 1800) {
            self::regenerate(false);
            $_SESSION['_created_at'] = time();
        }
    }

    public static function regenerate(bool $destroyOld = false): void
    {
        self::start();
        session_regenerate_id($destroyOld);
    }

    public static function get(string $key, mixed $default = null): mixed
    {
        self::start();
        return $_SESSION[$key] ?? $default;
    }

    public static function set(string $key, mixed $value): void
    {
        self::start();
        $_SESSION[$key] = $value;
    }

    public static function has(string $key): bool
    {
        self::start();
        return isset($_SESSION[$key]);
    }

    public static function remove(string $key): void
    {
        self::start();
        unset($_SESSION[$key]);
    }

    public static function setFlash(string $type, string $message): void
    {
        self::start();
        if (!isset($_SESSION['_flash'][$type])) {
            $_SESSION['_flash'][$type] = [];
        }
        $_SESSION['_flash'][$type][] = $message;
    }

    public static function getFlash(string $type): array
    {
        self::start();
        $messages = $_SESSION['_flash'][$type] ?? [];
        unset($_SESSION['_flash'][$type]);
        return $messages;
    }

    public static function hasFlash(string $type): bool
    {
        self::start();
        return !empty($_SESSION['_flash'][$type]);
    }

    public static function getCsrfToken(): string
    {
        self::start();
        if (empty($_SESSION['_csrf_token'])) {
            $_SESSION['_csrf_token'] = bin2hex(random_bytes(32));
        }
        return (string)$_SESSION['_csrf_token'];
    }

    public static function verifyCsrfToken(?string $token): bool
    {
        self::start();
        $stored = self::getCsrfToken();
        if ($token === null || $token === '') {
            return false;
        }
        return hash_equals($stored, $token);
    }

    public static function setUser(array $user): void
    {
        self::start();
        self::regenerate(true);
        $_SESSION['_user'] = $user;
    }

    public static function getUser(): ?array
    {
        self::start();
        return $_SESSION['_user'] ?? null;
    }

    public static function isAuthenticated(): bool
    {
        self::start();
        return !empty($_SESSION['_user']);
    }

    public static function getUserRole(): ?string
    {
        $user = self::getUser();
        return $user['role'] ?? null;
    }

    public static function destroy(): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            $_SESSION = [];
            if (ini_get('session.use_cookies')) {
                $params = session_get_cookie_params();
                setcookie(
                    session_name(),
                    '',
                    time() - 42000,
                    $params['path'],
                    $params['domain'],
                    $params['secure'],
                    $params['httponly']
                );
            }
            session_destroy();
            self::$started = false;
        }
    }
}
