<div class="card form">
  <h2>Entrar</h2>
  <p class="mb-1" style="color:#6b7280;">Use seu email e senha para acessar.</p>
  <form method="post" action="?controller=auth&action=doLogin">
    <div class="row">
      <label for="email">Email</label>
      <input type="email" id="email" name="email" required placeholder="seu@email.com">
    </div>
    <div class="row">
      <label for="senha">Senha</label>
      <input type="password" id="senha" name="senha" required placeholder="Sua senha">
    </div>
    <button class="btn btn-primary" type="submit">Acessar</button>
  </form>
  <div class="mt-2" style="color:#6b7280;">
    <small>Dica: primeiro acesso usa email <code>admin@localhost</code> e senha <code>admin123</code>.</small>
  </div>
</div>