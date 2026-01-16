<?php
$current = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
$current = rtrim($current, '/') ?: '/';

function nav_chip(string $href, string $label, string $current): string {
    $active = ($href === $current) ? 'chip active' : 'chip';
    return '<a class="' . $active . '" href="' . h($href) . '">' . h($label) . '</a>';
}
?>
<!doctype html>
<html lang="pt-br">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title><?= h($appName ?? 'Mentoria MPQ') ?></title>
  <link rel="stylesheet" href="/assets/app.css" />
</head>
<body>
<div class="topbar">
  <div class="topbar-inner">
    <div class="brand">
      <?= h($appName ?? 'Mentoria MPQ') ?>
      <small><?= ($isAdmin ?? false) ? 'Admin' : 'Aluno' ?></small>
    </div>
    <div class="nav">
      <?php if (!empty($authUser) && empty($isAdmin)): ?>
        <?= nav_chip('/dashboard', 'Inicio', $current) ?>
        <?= nav_chip('/aulas', 'Aulas', $current) ?>
        <?= nav_chip('/materiais', 'Materiais', $current) ?>
        <?= nav_chip('/novidades', 'Novidades', $current) ?>
        <?= nav_chip('/perfil', 'Perfil', $current) ?>
      <?php elseif (!empty($authUser) && !empty($isAdmin)): ?>
        <?= nav_chip('/admin', 'Admin', $current) ?>
        <?= nav_chip('/admin/usuarios', 'Usuarios', $current) ?>
        <?= nav_chip('/admin/aulas', 'Aulas', $current) ?>
        <?= nav_chip('/admin/materiais', 'Materiais', $current) ?>
        <?= nav_chip('/admin/novidades', 'Novidades', $current) ?>
        <?= nav_chip('/admin/permissoes', 'Permissoes', $current) ?>
      <?php endif; ?>
      <?php if (!empty($authUser)): ?>
        <a class="chip" href="/logout">Sair</a>
      <?php endif; ?>
    </div>
  </div>
</div>

<div class="container">
  <?php if (!empty($flash)): ?>
    <div class="card" data-flash-autohide>
      <div class="<?= (($flash['type'] ?? '') === 'success') ? 'success' : 'error' ?>">
        <?= h($flash['message'] ?? '') ?>
      </div>
    </div>
  <?php endif; ?>

  <?php include $file; ?>
</div>

<script src="/assets/app.js"></script>
</body>
</html>
