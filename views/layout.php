<?php
$base = rtrim(dirname($_SERVER['SCRIPT_NAME'] ?? ''), '/\\');
$isPublic = (substr($base, -7) === '/public');
$assetHref = ($base === '' || $base === '/')
  ? '/assets/style.css'
  : ($isPublic ? $base . '/assets/style.css' : $base . '/public/assets/style.css');
?><!doctype html>
<html lang="pt-br">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Farmácia beneficente</title>
  <link rel="stylesheet" href="<?php echo htmlspecialchars($assetHref); ?>">
</head>
<body>
  <header class="header">
    <div class="container" style="margin:0 auto;">
      <span class="brand">Farmacia beneficente</span>
      <a class="btn btn-outline" href="?controller=about&action=index" style="margin-left:12px;">Sobre</a>
      <div class="right">
        <?php if (isset($_SESSION['user'])): ?>
          <span>Olá, <?php echo htmlspecialchars($_SESSION['user']['nome']); ?></span>
          <a class="btn btn-outline" href="?controller=auth&action=logout">Sair</a>
        <?php else: ?>
          <a class="btn btn-outline" href="?controller=auth&action=login">Entrar</a>
        <?php endif; ?>
      </div>
    </div>
  </header>

  <div class="container">
    <div class="menu card">
      <ul>
        <li><a class="active" href="?">Início</a></li>
        <?php $isLogged = isset($_SESSION['user']); $loginStr = strtolower($_SESSION['user']['login'] ?? ''); $isAtt = strpos($loginStr, 'atendente') !== false; ?>
        <?php if (!$isLogged): ?>
          <!-- Antes do login, apenas Início visível -->
        <?php elseif ($isAtt): ?>
          <li><a href="?controller=table&action=index&name=dispensacoes">Dispensações</a></li>
        <?php else: ?>
          <li><a href="?controller=table&action=index&name=medicamentos">Medicamentos</a></li>
          <li><a href="?controller=table&action=index&name=lotes">Lotes</a></li>
          <li><a href="?controller=table&action=index&name=entradas">Entradas</a></li>
          <li><a href="?controller=table&action=index&name=dispensacoes">Dispensações</a></li>
          <li><a href="?controller=table&action=index&name=laboratorios">Laboratórios</a></li>
          <li><a href="?controller=table&action=index&name=classes_terapeuticas">Classes terapêuticas</a></li>
          <li><a href="?controller=table&action=index&name=fornecedores">Fornecedores</a></li>
          <li><a href="?controller=table&action=index&name=pacientes">Pacientes</a></li>
          <li><a href="?controller=table&action=index&name=vw_estoque_por_lote">Estoque por lote</a></li>
          <li><a href="?controller=table&action=index&name=vw_estoque_por_medicamento">Estoque por medicamento</a></li>
          <li><a href="?controller=table&action=index&name=vw_alerta_validade_mes_atual">Alertas mês atual</a></li>
          <li><a href="?controller=table&action=index&name=vw_alerta_validade">Alertas de validade</a></li>
          <li><a href="?controller=table&action=index&name=vw_alerta_estoque_baixo">Alertas de estoque</a></li>
          <li><a href="?controller=table&action=index&name=usuarios">Usuários</a></li>
        <?php endif; ?>
      </ul>
    </div>
  </div>

  <main class="container">
    <?php if (isset($error) && $error) { echo '<div class="error">' . htmlspecialchars($error) . '</div>'; } ?>
    <?php require $viewFile; ?>
  </main>
</body>
</html>