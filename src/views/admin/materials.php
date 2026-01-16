<div class="card">
  <h3>Materiais</h3>
  <p class="help">Envie materiais de apoio para liberar por usuario.</p>
</div>

<div class="card">
  <h3>Upload de material</h3>
  <form class="form" method="post" action="/admin/materiais/upload" enctype="multipart/form-data">
    <input type="hidden" name="_csrf" value="<?= h($csrf) ?>" />
    <input class="input" name="title" placeholder="titulo" required />
    <textarea name="description" placeholder="descricao (opcional)"></textarea>
    <input class="input" type="file" name="file" required />
    <button class="btn primary" type="submit">Enviar</button>
  </form>
</div>

<div class="grid">
  <?php foreach (($materials ?? []) as $m): ?>
    <div class="card">
      <h3><?= h($m['title'] ?? '') ?></h3>
      <p><?= h($m['description'] ?? '') ?></p>
      <div class="meta"><span><?= h($m['original_name'] ?? '') ?></span></div>
      <div class="row" style="margin-top:12px">
        <form method="post" action="/admin/materiais/excluir">
          <input type="hidden" name="_csrf" value="<?= h($csrf) ?>" />
          <input type="hidden" name="id" value="<?= (int)$m['id'] ?>" />
          <button class="btn danger" type="submit">Excluir</button>
        </form>
      </div>
    </div>
  <?php endforeach; ?>
  <?php if (empty($materials)): ?>
    <div class="card"><p>Nenhum material cadastrado ainda.</p></div>
  <?php endif; ?>
</div>
