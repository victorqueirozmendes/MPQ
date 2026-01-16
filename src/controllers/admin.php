<?php
declare(strict_types=1);

function admin_users(): void
{
    $users = db()->query('SELECT id,email,role,is_active,created_at FROM users ORDER BY created_at DESC')->fetchAll();
    view('admin/users', ['users' => $users]);
}

function admin_user_create(): void
{
    csrf_verify();
    $email = strtolower(post('email'));
    $password = post('password');
    $role = post('role', 'student');
    if ($email === '' || $password === '') {
        flash_set('error', 'Informe email e senha.');
        redirect('/admin/usuarios');
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        flash_set('error', 'Email invalido.');
        redirect('/admin/usuarios');
    }
    if (strlen($password) < 8) {
        flash_set('error', 'Senha deve ter pelo menos 8 caracteres.');
        redirect('/admin/usuarios');
    }
    if (!in_array($role, ['admin', 'student'], true)) $role = 'student';

    $hash = password_hash($password, PASSWORD_DEFAULT);
    try {
        $stmt = db()->prepare('INSERT INTO users (email,password_hash,role) VALUES (?,?,?)');
        $stmt->execute([$email, $hash, $role]);
        flash_set('success', 'Usuario criado.');
    } catch (Throwable $e) {
        flash_set('error', 'Nao foi possivel criar (email pode ja existir).');
    }
    redirect('/admin/usuarios');
}

function admin_user_reset_password(): void
{
    csrf_verify();
    $id = (int)post('id');
    $password = post('password');
    if ($id <= 0 || $password === '') {
        flash_set('error', 'Dados invalidos.');
        redirect('/admin/usuarios');
    }
    if (strlen($password) < 8) {
        flash_set('error', 'Senha deve ter pelo menos 8 caracteres.');
        redirect('/admin/usuarios');
    }
    $hash = password_hash($password, PASSWORD_DEFAULT);
    $stmt = db()->prepare('UPDATE users SET password_hash=? WHERE id=?');
    $stmt->execute([$hash, $id]);
    flash_set('success', 'Senha resetada.');
    redirect('/admin/usuarios');
}

function admin_user_toggle_active(): void
{
    csrf_verify();
    $id = (int)post('id');
    if ($id <= 0) redirect('/admin/usuarios');
    $stmt = db()->prepare('UPDATE users SET is_active = IF(is_active=1,0,1) WHERE id=?');
    $stmt->execute([$id]);
    flash_set('success', 'Status atualizado.');
    redirect('/admin/usuarios');
}

function admin_lessons(): void
{
    $lessons = db()->query('SELECT * FROM lessons ORDER BY created_at DESC')->fetchAll();
    view('admin/lessons', ['lessons' => $lessons]);
}

function admin_lesson_create(): void
{
    csrf_verify();
    $title = post('title');
    $youtubeUrl = post('youtube_url');
    $description = post('description');
    $youtubeId = youtube_extract_id($youtubeUrl);
    if ($title === '' || $youtubeUrl === '' || !$youtubeId) {
        flash_set('error', 'Informe titulo e um link do YouTube valido.');
        redirect('/admin/aulas');
    }
    $stmt = db()->prepare('INSERT INTO lessons (title,youtube_url,youtube_id,description) VALUES (?,?,?,?)');
    $stmt->execute([$title, $youtubeUrl, $youtubeId, $description]);
    flash_set('success', 'Aula criada.');
    redirect('/admin/aulas');
}

function admin_lesson_delete(): void
{
    csrf_verify();
    $id = (int)post('id');
    if ($id <= 0) redirect('/admin/aulas');
    $stmt = db()->prepare('DELETE FROM lessons WHERE id=?');
    $stmt->execute([$id]);
    flash_set('success', 'Aula excluida.');
    redirect('/admin/aulas');
}

function admin_materials(): void
{
    $materials = db()->query('SELECT * FROM materials ORDER BY created_at DESC')->fetchAll();
    view('admin/materials', ['materials' => $materials]);
}

function admin_material_upload(): void
{
    csrf_verify();
    $title = post('title');
    $description = post('description');
    if ($title === '') {
        flash_set('error', 'Informe um titulo.');
        redirect('/admin/materiais');
    }
    $maxBytes = (int)(env_get('UPLOAD_MAX_BYTES', '10485760') ?? '10485760');

    $f = $_FILES['file'] ?? null;
    if (!is_array($f) || ($f['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
        flash_set('error', 'Selecione um arquivo para upload.');
        redirect('/admin/materiais');
    }
    if (($f['size'] ?? 0) > $maxBytes) {
        flash_set('error', 'Arquivo muito grande.');
        redirect('/admin/materiais');
    }

    $tmp = (string)$f['tmp_name'];
    $orig = (string)$f['name'];
    $mime = (string)($f['type'] ?? 'application/octet-stream');

    $allowedExt = ['pdf','doc','docx','ppt','pptx','xls','xlsx','zip','rar','png','jpg','jpeg','webp'];
    $ext = strtolower(pathinfo($orig, PATHINFO_EXTENSION));
    if (!in_array($ext, $allowedExt, true)) {
        flash_set('error', 'Extensao nao permitida.');
        redirect('/admin/materiais');
    }

    $dir = __DIR__ . '/../../public/uploads/materials';
    if (!is_dir($dir) && !mkdir($dir, 0775, true)) {
        flash_set('error', 'Nao foi possivel criar diretorio de upload.');
        redirect('/admin/materiais');
    }
    $name = bin2hex(random_bytes(16)) . '.' . $ext;
    $dest = $dir . '/' . $name;
    if (!move_uploaded_file($tmp, $dest)) {
        flash_set('error', 'Falha ao salvar o arquivo.');
        redirect('/admin/materiais');
    }

    $stmt = db()->prepare('INSERT INTO materials (title,description,file_path,original_name,mime_type,size_bytes) VALUES (?,?,?,?,?,?)');
    $stmt->execute([$title, $description, '/uploads/materials/' . $name, $orig, $mime, (int)$f['size']]);
    flash_set('success', 'Material enviado.');
    redirect('/admin/materiais');
}

function admin_material_delete(): void
{
    csrf_verify();
    $id = (int)post('id');
    if ($id <= 0) redirect('/admin/materiais');
    $stmt = db()->prepare('SELECT file_path FROM materials WHERE id=?');
    $stmt->execute([$id]);
    $m = $stmt->fetch();
    if ($m) {
        $full = __DIR__ . '/../../public' . $m['file_path'];
        if (is_file($full)) @unlink($full);
    }
    $stmt = db()->prepare('DELETE FROM materials WHERE id=?');
    $stmt->execute([$id]);
    flash_set('success', 'Material excluido.');
    redirect('/admin/materiais');
}

function admin_news(): void
{
    $items = db()->query('SELECT * FROM news ORDER BY created_at DESC')->fetchAll();
    view('admin/news', ['items' => $items]);
}

function admin_news_create(): void
{
    csrf_verify();
    $title = post('title');
    $body = post('body');
    if ($title === '' || $body === '') {
        flash_set('error', 'Informe titulo e conteudo.');
        redirect('/admin/novidades');
    }
    $stmt = db()->prepare('INSERT INTO news (title,body) VALUES (?,?)');
    $stmt->execute([$title, $body]);
    flash_set('success', 'Novidade publicada.');
    redirect('/admin/novidades');
}

function admin_news_delete(): void
{
    csrf_verify();
    $id = (int)post('id');
    if ($id <= 0) redirect('/admin/novidades');
    $stmt = db()->prepare('DELETE FROM news WHERE id=?');
    $stmt->execute([$id]);
    flash_set('success', 'Novidade excluida.');
    redirect('/admin/novidades');
}

function admin_access(): void
{
    $userId = int_query('user_id');
    $users = db()->query('SELECT id,email,role,is_active FROM users ORDER BY email ASC')->fetchAll();
    $lessons = db()->query('SELECT id,title FROM lessons ORDER BY created_at DESC')->fetchAll();
    $materials = db()->query('SELECT id,title FROM materials ORDER BY created_at DESC')->fetchAll();

    $lessonAllowed = [];
    $materialAllowed = [];
    if ($userId > 0) {
        $stmt = db()->prepare('SELECT lesson_id, allowed FROM user_lesson_access WHERE user_id=?');
        $stmt->execute([$userId]);
        foreach ($stmt->fetchAll() as $row) $lessonAllowed[(int)$row['lesson_id']] = (int)$row['allowed'];

        $stmt = db()->prepare('SELECT material_id, allowed FROM user_material_access WHERE user_id=?');
        $stmt->execute([$userId]);
        foreach ($stmt->fetchAll() as $row) $materialAllowed[(int)$row['material_id']] = (int)$row['allowed'];
    }

    view('admin/access', [
        'users' => $users,
        'userId' => $userId,
        'lessons' => $lessons,
        'materials' => $materials,
        'lessonAllowed' => $lessonAllowed,
        'materialAllowed' => $materialAllowed,
    ]);
}

function admin_access_toggle_lesson(): void
{
    csrf_verify();
    $userId = (int)post('user_id');
    $lessonId = (int)post('lesson_id');
    if ($userId <= 0 || $lessonId <= 0) redirect('/admin/permissoes');
    $stmt = db()->prepare(
        'INSERT INTO user_lesson_access (user_id, lesson_id, allowed) VALUES (?,?,1)
         ON DUPLICATE KEY UPDATE allowed = IF(allowed=1,0,1)'
    );
    $stmt->execute([$userId, $lessonId]);
    redirect('/admin/permissoes?user_id=' . $userId);
}

function admin_access_toggle_material(): void
{
    csrf_verify();
    $userId = (int)post('user_id');
    $materialId = (int)post('material_id');
    if ($userId <= 0 || $materialId <= 0) redirect('/admin/permissoes');
    $stmt = db()->prepare(
        'INSERT INTO user_material_access (user_id, material_id, allowed) VALUES (?,?,1)
         ON DUPLICATE KEY UPDATE allowed = IF(allowed=1,0,1)'
    );
    $stmt->execute([$userId, $materialId]);
    redirect('/admin/permissoes?user_id=' . $userId);
}
