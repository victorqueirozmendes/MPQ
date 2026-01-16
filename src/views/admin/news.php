<div class="card">
  <h3>Novidades</h3>
  <p class="help">Publique atualizacoes para os alunos.</p>
</div>

<div class="card">
  <h3>Nova novidade</h3>
  <form class="form" method="post" action="/admin/novidades/criar">
    <input type="hidden" name="_csrf" value="<?= h($csrf) ?>" />
    <input class="input" name="title" placeholder="titulo" required />
    <textarea name="body" placeholder="conteudo" required></textarea>
    <button class="btn primary" type="submit">Publicar</button>
  </form>
</div>

<div class="grid">
  <?php foreach (($items ?? []) as $n): ?>
    <div class="card">
      <h3><?= h($n['title'] ?? '') ?></h3>
      <p><?= nl2br(h($n['body'] ?? '')) ?></p>
      <div class="row" style="margin-top:12px">
        <form method="post" action="/admin/novidades/excluir">
          <input type="hidden" name="_csrf" value="<?= h($csrf) ?>" />
          <input type="hidden" name="id" value="<?= (int)$n['id'] ?>" />
          <button class="btn danger" type="submit">Excluir</button>
        </form>
      </div>
    </div>
  <?php endforeach; ?>
  <?php if (empty($items)): ?>
    <div class="card"><p>Nenhuma novidade publicada ainda.</p></div>
  <?php endif; ?>
</div>
