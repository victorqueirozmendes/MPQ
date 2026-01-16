<div class="row" style="margin-bottom:12px">
  <h3 style="margin:0">Novidades</h3>
  <div class="spacer"></div>
</div>

<div class="grid">
  <?php foreach (($items ?? []) as $n): ?>
    <div class="card">
      <h3><?= h($n['title'] ?? '') ?></h3>
      <p><?= nl2br(h($n['body'] ?? '')) ?></p>
      <div class="meta"><span><?= h($n['created_at'] ?? '') ?></span></div>
    </div>
  <?php endforeach; ?>
  <?php if (empty($items)): ?>
    <div class="card"><p>Nenhuma novidade publicada ainda.</p></div>
  <?php endif; ?>
</div>
