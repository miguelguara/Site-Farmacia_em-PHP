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
      <div class="icon">🏭</div>
      <h3>Laboratórios</h3>
      <p>Cadastro de laboratórios</p>
      <a class="btn btn-outline" href="?controller=table&action=index&name=laboratorios">Acessar</a>
    </div>
    <div class="tile">
      <div class="icon">🧪</div>
      <h3>Classes terapêuticas</h3>
      <p>Cadastro de classes</p>
      <a class="btn btn-outline" href="?controller=table&action=index&name=classes_terapeuticas">Acessar</a>
    </div>
    <div class="tile">
      <div class="icon">🚚</div>
      <h3>Fornecedores</h3>
      <p>Cadastro de fornecedores</p>
      <a class="btn btn-outline" href="?controller=table&action=index&name=fornecedores">Acessar</a>
    </div>
    <div class="tile">
      <div class="icon">📦</div>
      <h3>Estoque por lote</h3>
      <p>Consulta de estoque por lote (somente leitura)</p>
      <a class="btn btn-outline" href="?controller=table&action=index&name=vw_estoque_por_lote">Acessar</a>
    </div>
    <div class="tile">
      <div class="icon">🧴</div>
      <h3>Estoque por medicamento</h3>
      <p>Consulta de estoque por medicamento (somente leitura)</p>
      <a class="btn btn-outline" href="?controller=table&action=index&name=vw_estoque_por_medicamento">Acessar</a>
    </div>
    <div class="tile">
      <div class="icon">⏳</div>
      <h3>Alertas de validade</h3>
      <p>Vencidos e próximos de vencer</p>
      <a class="btn btn-outline" href="?controller=table&action=index&name=vw_alerta_validade">Acessar</a>
    </div>
    <div class="tile">
      <div class="icon">📅</div>
      <h3>Alertas do mês</h3>
      <p>Lotes que vencem no mês atual</p>
      <a class="btn btn-outline" href="?controller=table&action=index&name=vw_alerta_validade_mes_atual">Acessar</a>
    </div>
    <div class="tile">
      <div class="icon">⚠️</div>
      <h3>Alertas de estoque</h3>
      <p>Estoque mínimo/≤10 unidades/≤20%</p>
      <a class="btn btn-outline" href="?controller=table&action=index&name=vw_alerta_estoque_baixo">Acessar</a>
    </div>
    <div class="tile">
      <div class="icon">👥</div>
      <h3>Pacientes</h3>
      <p>Gerenciar cadastro de pacientes</p>
      <a class="btn btn-outline" href="?controller=table&action=index&name=pacientes">Acessar</a>
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