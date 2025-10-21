<?php
$driver = Database::driver();
$schema = Database::schema();
?>
<div class="card">
  <div class="hero">Bem-vindo ao Sistema de Gestão de Farmácia</div>
  <p class="sub">Escolha uma opção no menu acima para começar.</p>

  <div class="grid mt-2">
    <div class="tile">
      <div class="icon">💊</div>
      <h3>Medicamentos</h3>
      <p>Gerenciar cadastro de medicamentos</p>
      <a class="btn btn-primary" href="?controller=table&action=index&name=medicamentos">Acessar</a>
    </div>
    <div class="tile">
      <div class="icon">⬇️</div>
      <h3>Entradas</h3>
      <p>Registrar entrada de medicamentos</p>
      <a class="btn btn-primary" href="?controller=table&action=index&name=entradas">Acessar</a>
    </div>
    <div class="tile">
      <div class="icon">➡️</div>
      <h3>Dispensações</h3>
      <p>Registrar dispensação de medicamentos</p>
      <a class="btn btn-danger" href="?controller=table&action=index&name=dispensacoes">Acessar</a>
    </div>
    <div class="tile">
      <div class="icon">🔬</div>
      <h3>Laboratório</h3>
      <p>Gerenciar exames e laudos de laboratório</p>
      <a class="btn btn-outline" href="?controller=table&action=index&name=laboratorio">Acessar</a>
    </div>
    <div class="tile">
      <div class="icon">⚠️</div>
      <h3>Alertas</h3>
      <p>Visualizar alertas do sistema</p>
      <a class="btn btn-outline" href="?controller=table&action=index&name=alertas">Acessar</a>
    </div>
    <div class="tile">
      <div class="icon">📊</div>
      <h3>Relatórios</h3>
      <p>Gerar relatórios do sistema</p>
      <a class="btn btn-outline" href="?controller=table&action=index&name=relatorios">Acessar</a>
    </div>
    <div class="tile">
      <div class="icon">👥</div>
      <h3>Pacientes</h3>
      <p>Gerenciar cadastro de pacientes</p>
      <a class="btn btn-outline" href="?controller=table&action=index&name=pacientes">Acessar</a>
    </div>
  </div>

  <div class="mt-2">
    <p style="color:#6b7280;">Driver: <strong><?php echo htmlspecialchars($driver); ?></strong> | Schema: <strong><?php echo htmlspecialchars($schema ?? 'database atual'); ?></strong></p>
  </div>
</div>