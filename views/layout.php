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
      <div class="right">
        <span>Olá, Miguel</span>
        <a class="btn btn-outline" href="#">Sair</a>
      </div>
    </div>
  </header>

  <div class="container">
    <div class="menu card">
      <ul>
        <li><a class="active" href="?">Início</a></li>
        <li><a href="?controller=table&action=index&name=medicamentos">Medicamentos</a></li>
        <li><a href="?controller=table&action=index&name=lotes">Lotes</a></li>
        <li><a href="?controller=table&action=index&name=entradas">Entradas</a></li>
        <li><a href="?controller=table&action=index&name=dispensacoes">Dispensações</a></li>
        <li><a href="?controller=table&action=index&name=laboratorio">Laboratório</a></li>
        <li><a href="?controller=table&action=index&name=pacientes">Pacientes</a></li>
        <li><a href="?controller=table&action=index&name=alertas">Alertas</a></li>
        <li><a href="?controller=table&action=index&name=relatorios">Relatórios</a></li>
        <li><a href="?controller=table&action=index&name=usuarios">Usuários</a></li>
      </ul>
    </div>
  </div>

  <main class="container">
    <?php if (isset($error) && $error) { echo '<div class="error">' . htmlspecialchars($error) . '</div>'; } ?>
    <?php require $viewFile; ?>
  </main>
</body>
</html>