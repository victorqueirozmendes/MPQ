<?php $target = (!empty($isAdmin)) ? '/admin/login' : '/login'; ?>
<div class="card">
  <h3><?= (!empty($isAdmin)) ? 'Login do Admin' : 'Login' ?></h3>
  <p class="help">Entre com email e senha.</p>
  <form class="form" method="post" action="<?= h($target) ?>">
    <input type="hidden" name="_csrf" value="<?= h($csrf) ?>" />
    <div>
      <div class="help">Email</div>
      <input class="input" type="email" name="email" required />
    </div>
    <div>
      <div class="help">Senha</div>
      <input class="input" type="password" name="password" required />
    </div>
    <div class="row">
      <button class="btn primary" type="submit">Entrar</button>
      <div class="spacer"></div>
      <?php if (empty($isAdmin)): ?>
        <a class="chip" href="/admin/login">Sou admin</a>
      <?php endif; ?>
    </div>
  </form>
</div>
