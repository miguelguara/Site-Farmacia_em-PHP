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
      <span class="brand-wrap">
        <img src="?controller=img&action=get&name=HeartSyringe.png" alt="Logo" />
        <span class="brand">Farmacia beneficente</span>
      </span>
      <a class="btn btn-outline" href="?controller=about&action=index" style="margin-left:12px;">Sobre</a>
      <div class="right">
        <button id="theme-toggle" class="btn btn-outline" aria-label="Alternar tema">
          <span class="theme-icon">🌙</span>
        </button>
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

  <script>
    // Dark mode functionality
    const themeToggle = document.getElementById('theme-toggle');
    const themeIcon = themeToggle.querySelector('.theme-icon');
    const html = document.documentElement;

    // Check for saved theme preference or default to light mode
    const currentTheme = localStorage.getItem('theme') || 'light';
    
    // Apply the saved theme on page load
    if (currentTheme === 'dark') {
      html.setAttribute('data-theme', 'dark');
      themeIcon.textContent = '☀️';
    }

    // Toggle theme function
    function toggleTheme() {
      const currentTheme = html.getAttribute('data-theme');
      const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
      
      html.setAttribute('data-theme', newTheme);
      localStorage.setItem('theme', newTheme);
      
      // Update icon
      themeIcon.textContent = newTheme === 'dark' ? '☀️' : '🌙';
    }

    // Add click event listener
    themeToggle.addEventListener('click', toggleTheme);
  </script>
</body>
</html>