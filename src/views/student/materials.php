<div class="row" style="margin-bottom:12px">
  <h3 style="margin:0">Materiais</h3>
  <div class="spacer"></div>
</div>

<div class="grid">
  <?php foreach (($materials ?? []) as $m): ?>
    <?php $allowed = ((int)($m['allowed'] ?? 0) === 1); ?>
    <div class="card">
      <h3><?= h($m['title'] ?? '') ?></h3>
      <p><?= h($m['description'] ?? '') ?></p>
      <div class="meta"><span><?= h($m['original_name'] ?? '') ?></span></div>
      <div class="row" style="margin-top:12px">
        <?php if ($allowed): ?>
          <a class="btn primary" href="/material?id=<?= (int)$m['id'] ?>">Baixar</a>
        <?php else: ?>
          <div class="lock"><span>&#128274; BLOQUEADO</span></div>
        <?php endif; ?>
      </div>
    </div>
  <?php endforeach; ?>
  <?php if (empty($materials)): ?>
    <div class="card"><p>Nenhum material cadastrado ainda.</p></div>
  <?php endif; ?>
</div>
