<?php
class Auth
{
    public static function login(array $user): void
    {
        session_regenerate_id(true);
        Session::set('user', [
            'id'   => $user['id'],
            'name' => $user['name'],
            'role' => $user['role'],
        ]);
    }

    public static function logout(): void
    {
        Session::destroy();
    }

    public static function check(): bool
    {
        return Session::get('user') !== null;
    }

    public static function user(): ?array
    {
        return Session::get('user');
    }

    public static function id(): ?int
    {
        return self::check() ? (int) self::user()['id'] : null;
    }

    public static function isAdmin(): bool
    {
        return self::check() && self::user()['role'] === 'admin';
    }

    public static function requireLogin(): void
    {
        if (!self::check()) {
            Session::flash('error', 'Please login first.');
            header('Location: ' . BASE_URL . '/auth/login');
            exit;
        }
    }

    public static function requireAdmin(): void
    {
        self::requireLogin();
        if (!self::isAdmin()) {
            http_response_code(403);
            echo '403 Forbidden';
            exit;
        }
    }
}
