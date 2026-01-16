<?php
declare(strict_types=1);

require __DIR__ . '/../src/env.php';
require __DIR__ . '/../src/db.php';

env_load(__DIR__ . '/../.env');

$email = $argv[1] ?? null;
$password = $argv[2] ?? null;

if (!is_string($email) || !is_string($password) || $email === '' || $password === '') {
    fwrite(STDERR, "Uso: php scripts/create_admin.php email senha\n");
    exit(1);
}
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    fwrite(STDERR, "Email invalido.\n");
    exit(1);
}
if (strlen($password) < 8) {
    fwrite(STDERR, "Senha deve ter pelo menos 8 caracteres.\n");
    exit(1);
}

$hash = password_hash($password, PASSWORD_DEFAULT);
try {
    $stmt = db()->prepare("INSERT INTO users (email,password_hash,role) VALUES (?,?,'admin')");
    $stmt->execute([strtolower($email), $hash]);
    echo "Admin criado: {$email}\n";
} catch (Throwable $e) {
    fwrite(STDERR, "Nao foi possivel criar (email pode ja existir).\n");
    exit(1);
}
