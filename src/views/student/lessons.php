<div class="row" style="margin-bottom:12px">
  <h3 style="margin:0">Aulas</h3>
  <div class="spacer"></div>
</div>

<div class="grid">
  <?php foreach (($lessons ?? []) as $l): ?>
    <?php $allowed = ((int)($l['allowed'] ?? 0) === 1); ?>
    <div class="card">
      <h3><?= h($l['title'] ?? '') ?></h3>
      <p><?= h($l['description'] ?? '') ?></p>
      <div class="meta"><span>YouTube</span></div>
      <div class="row" style="margin-top:12px">
        <?php if ($allowed): ?>
          <a class="btn primary" href="/aula?id=<?= (int)$l['id'] ?>">Assistir</a>
        <?php else: ?>
          <div class="lock"><span>&#128274; BLOQUEADO</span></div>
        <?php endif; ?>
      </div>
    </div>
  <?php endforeach; ?>
  <?php if (empty($lessons)): ?>
    <div class="card"><p>Nenhuma aula cadastrada ainda.</p></div>
  <?php endif; ?>
</div>
