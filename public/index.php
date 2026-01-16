<?php
declare(strict_types=1);

require __DIR__ . '/../src/bootstrap.php';

$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
$path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
$path = rtrim($path, '/') ?: '/';

if ($path === '/') {
    if (auth_user()) redirect('/dashboard');
    redirect('/login');
}

if ($path === '/login' && $method === 'GET') view('auth/login', ['isAdmin' => false]);
elseif ($path === '/login' && $method === 'POST') handle_login(false);
elseif ($path === '/logout') handle_logout();
elseif ($path === '/dashboard') require_login(fn() => view('student/dashboard'));
elseif ($path === '/aulas') require_login(fn() => student_lessons());
elseif ($path === '/aula') require_login(fn() => student_lesson_show());
elseif ($path === '/materiais') require_login(fn() => student_materials());
elseif ($path === '/material') require_login(fn() => student_material_download());
elseif ($path === '/novidades') require_login(fn() => student_news());
elseif ($path === '/perfil' && $method === 'GET') require_login(fn() => student_profile_form());
elseif ($path === '/perfil' && $method === 'POST') require_login(fn() => student_profile_save());

elseif ($path === '/admin/login' && $method === 'GET') view('auth/login', ['isAdmin' => true]);
elseif ($path === '/admin/login' && $method === 'POST') handle_login(true);
elseif ($path === '/admin' && $method === 'GET') require_admin(fn() => view('admin/dashboard'));

elseif ($path === '/admin/usuarios') require_admin(fn() => admin_users());
elseif ($path === '/admin/usuarios/criar' && $method === 'POST') require_admin(fn() => admin_user_create());
elseif ($path === '/admin/usuarios/resetar-senha' && $method === 'POST') require_admin(fn() => admin_user_reset_password());
elseif ($path === '/admin/usuarios/toggle' && $method === 'POST') require_admin(fn() => admin_user_toggle_active());

elseif ($path === '/admin/aulas') require_admin(fn() => admin_lessons());
elseif ($path === '/admin/aulas/criar' && $method === 'POST') require_admin(fn() => admin_lesson_create());
elseif ($path === '/admin/aulas/excluir' && $method === 'POST') require_admin(fn() => admin_lesson_delete());

elseif ($path === '/admin/materiais') require_admin(fn() => admin_materials());
elseif ($path === '/admin/materiais/upload' && $method === 'POST') require_admin(fn() => admin_material_upload());
elseif ($path === '/admin/materiais/excluir' && $method === 'POST') require_admin(fn() => admin_material_delete());

elseif ($path === '/admin/novidades') require_admin(fn() => admin_news());
elseif ($path === '/admin/novidades/criar' && $method === 'POST') require_admin(fn() => admin_news_create());
elseif ($path === '/admin/novidades/excluir' && $method === 'POST') require_admin(fn() => admin_news_delete());

elseif ($path === '/admin/permissoes') require_admin(fn() => admin_access());
elseif ($path === '/admin/permissoes/toggle-aula' && $method === 'POST') require_admin(fn() => admin_access_toggle_lesson());
elseif ($path === '/admin/permissoes/toggle-material' && $method === 'POST') require_admin(fn() => admin_access_toggle_material());

else {
    http_response_code(404);
    view('errors/404');
}
