<div class="card">
  <h3>Permissoes</h3>
  <p class="help">Selecione um usuario e libere/bloqueie aulas e materiais.</p>
</div>

<div class="card">
  <form class="form" method="get" action="/admin/permissoes">
    <select class="input" name="user_id" required>
      <option value="">Selecione um usuario</option>
      <?php foreach (($users ?? []) as $u): ?>
        <option value="<?= (int)$u['id'] ?>" <?= ((int)$userId === (int)$u['id']) ? 'selected' : '' ?>>
          <?= h($u['email'] ?? '') ?> (<?= h($u['role'] ?? '') ?>)
        </option>
      <?php endforeach; ?>
    </select>
    <button class="btn primary" type="submit">Carregar</button>
  </form>
</div>

<?php if (!empty($userId) && (int)$userId > 0): ?>
  <div class="card">
    <h3>Aulas</h3>
    <table class="table">
      <thead><tr><th>Aula</th><th>Status</th><th></th></tr></thead>
      <tbody>
      <?php foreach (($lessons ?? []) as $l): ?>
        <?php $allowed = ((int)($lessonAllowed[(int)$l['id']] ?? 0) === 1); ?>
        <tr>
          <td><?= h($l['title'] ?? '') ?></td>
          <td><?= $allowed ? 'liberada' : 'bloqueada' ?></td>
          <td>
            <form method="post" action="/admin/permissoes/toggle-aula">
              <input type="hidden" name="_csrf" value="<?= h($csrf) ?>" />
              <input type="hidden" name="user_id" value="<?= (int)$userId ?>" />
              <input type="hidden" name="lesson_id" value="<?= (int)$l['id'] ?>" />
              <button class="btn small <?= $allowed ? 'danger' : 'ok' ?>" type="submit"><?= $allowed ? 'Bloquear' : 'Liberar' ?></button>
            </form>
          </td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  </div>

  <div class="card">
    <h3>Materiais</h3>
    <table class="table">
      <thead><tr><th>Material</th><th>Status</th><th></th></tr></thead>
      <tbody>
      <?php foreach (($materials ?? []) as $m): ?>
        <?php $allowed = ((int)($materialAllowed[(int)$m['id']] ?? 0) === 1); ?>
        <tr>
          <td><?= h($m['title'] ?? '') ?></td>
          <td><?= $allowed ? 'liberado' : 'bloqueado' ?></td>
          <td>
            <form method="post" action="/admin/permissoes/toggle-material">
              <input type="hidden" name="_csrf" value="<?= h($csrf) ?>" />
              <input type="hidden" name="user_id" value="<?= (int)$userId ?>" />
              <input type="hidden" name="material_id" value="<?= (int)$m['id'] ?>" />
              <button class="btn small <?= $allowed ? 'danger' : 'ok' ?>" type="submit"><?= $allowed ? 'Bloquear' : 'Liberar' ?></button>
            </form>
          </td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  </div>
<?php endif; ?>
