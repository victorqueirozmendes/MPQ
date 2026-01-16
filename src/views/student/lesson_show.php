<?php $videoId = $lesson['youtube_id'] ?? ''; ?>
<div class="card">
  <h3><?= h($lesson['title'] ?? '') ?></h3>
  <p><?= h($lesson['description'] ?? '') ?></p>
</div>

<div class="card">
  <iframe class="video"
    src="https://www.youtube.com/embed/<?= h($videoId) ?>"
    title="YouTube player"
    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
    allowfullscreen></iframe>
</div>
