<div class="card">
  <h3>Aulas</h3>
  <p class="help">Cadastre aulas por link do YouTube.</p>
</div>

<div class="card">
  <h3>Nova aula</h3>
  <form class="form" method="post" action="/admin/aulas/criar">
    <input type="hidden" name="_csrf" value="<?= h($csrf) ?>" />
    <input class="input" name="title" placeholder="titulo" required />
    <input class="input" name="youtube_url" placeholder="link do YouTube" required />
    <textarea name="description" placeholder="descricao (opcional)"></textarea>
    <button class="btn primary" type="submit">Salvar</button>
  </form>
</div>

<div class="grid">
  <?php foreach (($lessons ?? []) as $l): ?>
    <div class="card">
      <h3><?= h($l['title'] ?? '') ?></h3>
      <p><?= h($l['description'] ?? '') ?></p>
      <div class="meta"><span><?= h($l['youtube_url'] ?? '') ?></span></div>
      <div class="row" style="margin-top:12px">
        <form method="post" action="/admin/aulas/excluir">
          <input type="hidden" name="_csrf" value="<?= h($csrf) ?>" />
          <input type="hidden" name="id" value="<?= (int)$l['id'] ?>" />
          <button class="btn danger" type="submit">Excluir</button>
        </form>
      </div>
    </div>
  <?php endforeach; ?>
  <?php if (empty($lessons)): ?>
    <div class="card"><p>Nenhuma aula cadastrada ainda.</p></div>
  <?php endif; ?>
</div>
