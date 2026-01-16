<?php
declare(strict_types=1);

function auth_user(): ?array
{
    $id = $_SESSION['user_id'] ?? null;
    if (!is_int($id) && !(is_string($id) && ctype_digit($id))) return null;
    $id = (int)$id;
    if ($id <= 0) return null;

    $stmt = db()->prepare('SELECT id,email,role,is_active,created_at FROM users WHERE id = ?');
    $stmt->execute([$id]);
    $user = $stmt->fetch();
    if (!$user) return null;
    if ((int)$user['is_active'] !== 1) return null;
    return $user;
}

function auth_is_admin(): bool
{
    $u = auth_user();
    return $u && ($u['role'] ?? '') === 'admin';
}

function require_login(callable $next): void
{
    if (!auth_user()) redirect('/login');
    $next();
}

function require_admin(callable $next): void
{
    if (!auth_user()) redirect('/admin/login');
    if (!auth_is_admin()) {
        http_response_code(403);
        view('errors/403');
        return;
    }
    $next();
}

function handle_login(bool $adminArea): void
{
    csrf_verify();
    $email = strtolower(post('email'));
    $password = post('password');
    if ($email === '' || $password === '') {
        flash_set('error', 'Informe email e senha.');
        redirect($adminArea ? '/admin/login' : '/login');
    }

    $stmt = db()->prepare('SELECT id,password_hash,role,is_active FROM users WHERE email = ? LIMIT 1');
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if (!$user || (int)$user['is_active'] !== 1 || !password_verify($password, (string)$user['password_hash'])) {
        flash_set('error', 'Login invalido.');
        redirect($adminArea ? '/admin/login' : '/login');
    }
    if ($adminArea && ($user['role'] ?? '') !== 'admin') {
        flash_set('error', 'Acesso restrito ao admin.');
        redirect('/admin/login');
    }

    session_regenerate_id(true);
    $_SESSION['user_id'] = (int)$user['id'];
    flash_set('success', 'Bem-vindo(a)!');
    redirect($adminArea ? '/admin' : '/dashboard');
}

function handle_logout(): void
{
    $_SESSION = [];
    if (session_id() !== '') session_destroy();
    redirect('/login');
}
