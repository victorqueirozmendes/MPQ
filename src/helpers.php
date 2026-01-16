<?php
declare(strict_types=1);

function h(?string $value): string
{
    return htmlspecialchars($value ?? '', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function redirect(string $to): void
{
    header('Location: ' . $to);
    exit;
}

function flash_set(string $type, string $message): void
{
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

function flash_get(): ?array
{
    if (!isset($_SESSION['flash'])) return null;
    $f = $_SESSION['flash'];
    unset($_SESSION['flash']);
    return $f;
}

function view(string $name, array $params = []): void
{
    $file = __DIR__ . '/views/' . $name . '.php';
    if (!is_file($file)) {
        http_response_code(500);
        echo 'View nao encontrada: ' . h($name);
        return;
    }

    $params['flash'] = flash_get();
    $params['appName'] = env_get('APP_NAME', 'Mentoria MPQ');
    $params['authUser'] = auth_user();
    $params['isAdmin'] = auth_is_admin();
    $params['csrf'] = csrf_token();

    extract($params, EXTR_SKIP);
    include __DIR__ . '/views/_layout.php';
}

function int_query(string $key, int $default = 0): int
{
    $v = $_GET[$key] ?? null;
    if ($v === null) return $default;
    if (!is_string($v) || $v === '') return $default;
    if (!ctype_digit($v)) return $default;
    return (int)$v;
}

function post(string $key, string $default = ''): string
{
    $v = $_POST[$key] ?? $default;
    return is_string($v) ? trim($v) : $default;
}

function youtube_extract_id(string $url): ?string
{
    $url = trim($url);
    if ($url === '') return null;

    if (preg_match('~youtu\.be/([A-Za-z0-9_-]{6,})~', $url, $m)) return $m[1];
    $parts = parse_url($url);
    if (is_array($parts) && ($parts['host'] ?? '')) {
        parse_str($parts['query'] ?? '', $q);
        $v = $q['v'] ?? null;
        if (is_string($v) && preg_match('~^[A-Za-z0-9_-]{6,}$~', $v)) return $v;
        if (isset($parts['path']) && preg_match('~/embed/([A-Za-z0-9_-]{6,})~', $parts['path'], $m)) return $m[1];
    }
    if (preg_match('~^[A-Za-z0-9_-]{6,}$~', $url)) return $url;
    return null;
}
