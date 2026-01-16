<?php $photoUrl = !empty($profile['photo_path']) ? $profile['photo_path'] : null; ?>

<div class="card">
  <h3>Perfil</h3>
  <p class="help">Complete seu cadastro. Voce pode trocar sua senha aqui.</p>
</div>

<div class="card">
  <form class="form" method="post" action="/perfil" enctype="multipart/form-data">
    <input type="hidden" name="_csrf" value="<?= h($csrf) ?>" />
    <div class="row">
      <?php if ($photoUrl): ?>
        <img class="avatar" src="<?= h($photoUrl) ?>" alt="Foto" />
      <?php else: ?>
        <div class="avatar" style="display:flex;align-items:center;justify-content:center;color:var(--muted)">sem foto</div>
      <?php endif; ?>
      <div style="flex:1">
        <div class="help">Foto (JPG/PNG/WEBP)</div>
        <input class="input" type="file" name="photo" accept="image/*" />
      </div>
    </div>
    <div><div class="help">Nome completo</div><input class="input" name="full_name" value="<?= h($profile['full_name'] ?? '') ?>" /></div>
    <div><div class="help">Telefone</div><input class="input" name="phone" value="<?= h($profile['phone'] ?? '') ?>" /></div>
    <div><div class="help">Data de nascimento</div><input class="input" type="date" name="birthdate" value="<?= h($profile['birthdate'] ?? '') ?>" /></div>
    <div><div class="help">Bio</div><textarea name="bio"><?= h($profile['bio'] ?? '') ?></textarea></div>
    <div><div class="help">Nova senha (opcional, min. 8)</div><input class="input" type="password" name="new_password" /></div>
    <div class="row"><button class="btn primary" type="submit">Salvar</button><div class="spacer"></div></div>
  </form>
</div>
