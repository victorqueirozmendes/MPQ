<?php
declare(strict_types=1);

require __DIR__ . '/env.php';
require __DIR__ . '/helpers.php';
require __DIR__ . '/db.php';
require __DIR__ . '/csrf.php';
require __DIR__ . '/auth.php';
require __DIR__ . '/controllers/student.php';
require __DIR__ . '/controllers/admin.php';

env_load(__DIR__ . '/../.env');

date_default_timezone_set('America/Sao_Paulo');

session_name('mpq_session');
session_set_cookie_params([
    'httponly' => true,
    'samesite' => 'Lax',
    'secure' => (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off'),
]);
session_start();

db();
