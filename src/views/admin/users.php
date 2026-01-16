<div class="card">
  <h3>Usuarios</h3>
  <p class="help">Crie usuarios, desative/ative e resete senha (senhas nao ficam visiveis).</p>
</div>

<div class="card">
  <h3>Criar usuario</h3>
  <form class="form" method="post" action="/admin/usuarios/criar">
    <input type="hidden" name="_csrf" value="<?= h($csrf) ?>" />
    <input class="input" type="email" name="email" placeholder="email" required />
    <input class="input" type="text" name="password" placeholder="senha inicial (min. 8)" required />
    <select class="input" name="role">
      <option value="student">Aluno</option>
      <option value="admin">Admin</option>
    </select>
    <button class="btn primary" type="submit">Criar</button>
  </form>
</div>

<div class="card">
  <h3>Lista</h3>
  <table class="table">
    <thead><tr><th>Email</th><th>Role</th><th>Status</th><th>Acoes</th></tr></thead>
    <tbody>
      <?php foreach (($users ?? []) as $u): ?>
        <tr>
          <td><?= h($u['email'] ?? '') ?></td>
          <td><?= h($u['role'] ?? '') ?></td>
          <td><?= ((int)($u['is_active'] ?? 0) === 1) ? 'ativo' : 'bloqueado' ?></td>
          <td>
            <form method="post" action="/admin/usuarios/toggle" style="display:inline">
              <input type="hidden" name="_csrf" value="<?= h($csrf) ?>" />
              <input type="hidden" name="id" value="<?= (int)$u['id'] ?>" />
              <button class="btn small" type="submit"><?= ((int)($u['is_active'] ?? 0) === 1) ? 'Bloquear' : 'Desbloquear' ?></button>
            </form>

            <form method="post" action="/admin/usuarios/resetar-senha" style="display:inline">
              <input type="hidden" name="_csrf" value="<?= h($csrf) ?>" />
              <input type="hidden" name="id" value="<?= (int)$u['id'] ?>" />
              <input class="input" style="width:160px;display:inline-block" name="password" placeholder="nova senha" />
              <button class="btn small" type="submit">Reset</button>
            </form>

            <a class="chip" href="/admin/permissoes?user_id=<?= (int)$u['id'] ?>">Permissoes</a>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>
