<?php
declare(strict_types=1);

function student_lessons(): void
{
    $u = auth_user();
    $stmt = db()->prepare(
        'SELECT l.*, COALESCE(ula.allowed, 0) AS allowed
         FROM lessons l
         LEFT JOIN user_lesson_access ula ON ula.lesson_id = l.id AND ula.user_id = ?
         ORDER BY l.created_at DESC'
    );
    $stmt->execute([(int)$u['id']]);
    $lessons = $stmt->fetchAll();
    view('student/lessons', ['lessons' => $lessons]);
}

function student_lesson_show(): void
{
    $u = auth_user();
    $id = int_query('id');
    if ($id <= 0) redirect('/aulas');

    $stmt = db()->prepare(
        'SELECT l.*, COALESCE(ula.allowed, 0) AS allowed
         FROM lessons l
         LEFT JOIN user_lesson_access ula ON ula.lesson_id = l.id AND ula.user_id = ?
         WHERE l.id = ?'
    );
    $stmt->execute([(int)$u['id'], $id]);
    $lesson = $stmt->fetch();
    if (!$lesson) {
        http_response_code(404);
        view('errors/404');
        return;
    }
    if ((int)$lesson['allowed'] !== 1) {
        flash_set('error', 'Essa aula esta bloqueada para sua conta.');
        redirect('/aulas');
    }
    view('student/lesson_show', ['lesson' => $lesson]);
}

function student_materials(): void
{
    $u = auth_user();
    $stmt = db()->prepare(
        'SELECT m.*, COALESCE(uma.allowed, 0) AS allowed
         FROM materials m
         LEFT JOIN user_material_access uma ON uma.material_id = m.id AND uma.user_id = ?
         ORDER BY m.created_at DESC'
    );
    $stmt->execute([(int)$u['id']]);
    $materials = $stmt->fetchAll();
    view('student/materials', ['materials' => $materials]);
}

function student_material_download(): void
{
    $u = auth_user();
    $id = int_query('id');
    if ($id <= 0) redirect('/materiais');

    $stmt = db()->prepare(
        'SELECT m.*, COALESCE(uma.allowed, 0) AS allowed
         FROM materials m
         LEFT JOIN user_material_access uma ON uma.material_id = m.id AND uma.user_id = ?
         WHERE m.id = ?'
    );
    $stmt->execute([(int)$u['id'], $id]);
    $m = $stmt->fetch();
    if (!$m) {
        http_response_code(404);
        view('errors/404');
        return;
    }
    if ((int)$m['allowed'] !== 1) {
        flash_set('error', 'Esse material esta bloqueado para sua conta.');
        redirect('/materiais');
    }

    $fullPath = __DIR__ . '/../../public' . $m['file_path'];
    if (!is_file($fullPath)) {
        flash_set('error', 'Arquivo nao encontrado no servidor.');
        redirect('/materiais');
    }

    header('Content-Type: ' . $m['mime_type']);
    header('Content-Length: ' . (string)$m['size_bytes']);
    header('Content-Disposition: attachment; filename="' . addslashes($m['original_name']) . '"');
    readfile($fullPath);
    exit;
}

function student_news(): void
{
    $items = db()->query('SELECT * FROM news ORDER BY created_at DESC')->fetchAll();
    view('student/news', ['items' => $items]);
}

function student_profile_form(): void
{
    $u = auth_user();
    $stmt = db()->prepare('SELECT * FROM user_profiles WHERE user_id = ?');
    $stmt->execute([(int)$u['id']]);
    $profile = $stmt->fetch() ?: [];
    view('student/profile', ['profile' => $profile]);
}

function student_profile_save(): void
{
    csrf_verify();
    $u = auth_user();
    $fullName = post('full_name');
    $phone = post('phone');
    $birthdate = post('birthdate');
    $bio = post('bio');
    $newPassword = post('new_password');

    $photoPath = null;
    if (isset($_FILES['photo']) && is_array($_FILES['photo']) && ($_FILES['photo']['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE) {
        $photoPath = upload_image('photo', '/uploads/avatars', 2000000);
    }

    $pdo = db();
    $pdo->beginTransaction();
    try {
        $stmt = $pdo->prepare('SELECT user_id, photo_path FROM user_profiles WHERE user_id = ?');
        $stmt->execute([(int)$u['id']]);
        $existing = $stmt->fetch();

        $finalPhoto = $photoPath ?? ($existing['photo_path'] ?? null);
        if ($existing) {
            $stmt = $pdo->prepare('UPDATE user_profiles SET full_name=?, phone=?, birthdate=NULLIF(?,\'\'), bio=?, photo_path=? WHERE user_id=?');
            $stmt->execute([$fullName, $phone, $birthdate, $bio, $finalPhoto, (int)$u['id']]);
        } else {
            $stmt = $pdo->prepare('INSERT INTO user_profiles (user_id, full_name, phone, birthdate, bio, photo_path) VALUES (?,?,?,NULLIF(?,\'\'),?,?)');
            $stmt->execute([(int)$u['id'], $fullName, $phone, $birthdate, $bio, $finalPhoto]);
        }

        if ($newPassword !== '') {
            if (strlen($newPassword) < 8) throw new RuntimeException('A nova senha deve ter pelo menos 8 caracteres.');
            $hash = password_hash($newPassword, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare('UPDATE users SET password_hash=? WHERE id=?');
            $stmt->execute([$hash, (int)$u['id']]);
        }

        $pdo->commit();
        flash_set('success', 'Perfil atualizado.');
        redirect('/perfil');
    } catch (Throwable $e) {
        $pdo->rollBack();
        flash_set('error', $e->getMessage());
        redirect('/perfil');
    }
}

function upload_image(string $field, string $publicDir, int $maxBytes): string
{
    $f = $_FILES[$field] ?? null;
    if (!is_array($f)) throw new RuntimeException('Upload invalido.');
    if (($f['error'] ?? UPLOAD_ERR_OK) !== UPLOAD_ERR_OK) throw new RuntimeException('Falha no upload.');
    if (($f['size'] ?? 0) > $maxBytes) throw new RuntimeException('Imagem muito grande.');

    $tmp = (string)($f['tmp_name'] ?? '');
    $info = @getimagesize($tmp);
    if ($info === false) throw new RuntimeException('Arquivo nao e uma imagem valida.');
    $mime = $info['mime'] ?? '';
    $ext = match ($mime) {
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/webp' => 'webp',
        default => null,
    };
    if ($ext === null) throw new RuntimeException('Formato de imagem nao suportado.');

    $base = rtrim(__DIR__ . '/../../public' . $publicDir, '/');
    if (!is_dir($base) && !mkdir($base, 0775, true)) throw new RuntimeException('Nao foi possivel criar diretorio de upload.');

    $name = bin2hex(random_bytes(16)) . '.' . $ext;
    $dest = $base . '/' . $name;
    if (!move_uploaded_file($tmp, $dest)) throw new RuntimeException('Nao foi possivel salvar a imagem.');
    return $publicDir . '/' . $name;
}
