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
    <div class="container" style="margin:0;">
      <span class="brand-wrap">
        <img src="?controller=img&action=get&name=HeartSyringe.png" alt="Logo" />
        <span class="brand">Farmacia beneficente</span>
      </span>
      <a class="btn btn-outline" href="?controller=about&action=index" style="margin-left:12px;">Sobre</a>
      <div class="right">
        <?php if (isset($_SESSION['user'])): ?>
          <span>Olá, <?php echo htmlspecialchars($_SESSION['user']['nome']); ?></span>
          <div class="theme-toggle-wrap" style="position:relative; display:inline-block; margin-left:8px;">
            <button id="theme-toggle" class="btn btn-outline" aria-label="Alternar tema">
              <span class="theme-icon" aria-hidden="true">☀️</span>
            </button>
            <div id="theme-menu" class="theme-menu" hidden>
              <button type="button" class="theme-option" data-theme="light">Branco</button>
              <button type="button" class="theme-option" data-theme="night">Noturno</button>
              <button type="button" class="theme-option" data-theme="black">Escuro</button>
            </div>
          </div>
          <a class="btn btn-outline" href="?controller=auth&action=logout" style="margin-left:8px;">Sair</a>
        <?php else: ?>
          <div class="theme-toggle-wrap" style="position:relative; display:inline-block; margin-left:8px;">
            <button id="theme-toggle" class="btn btn-outline" aria-label="Alternar tema">
              <span class="theme-icon" aria-hidden="true">☀️</span>
            </button>
            <div id="theme-menu" class="theme-menu" hidden>
              <button type="button" class="theme-option" data-theme="light">Branco</button>
              <button type="button" class="theme-option" data-theme="night">Noturno</button>
              <button type="button" class="theme-option" data-theme="black">Escuro</button>
            </div>
          </div>
          <a class="btn btn-outline" href="?controller=auth&action=login" style="margin-left:8px;">Entrar</a>
        <?php endif; ?>
      </div>
    </div>
  </header>

  <div class="container app">
    <aside class="sidebar">
      <div class="menu card">
        <ul>
          <?php 
            $isLogged = isset($_SESSION['user']); 
            $loginStr = strtolower($_SESSION['user']['login'] ?? ''); 
            $isAtt = strpos($loginStr, 'atendente') !== false; 
            $ctl = strtolower($_GET['controller'] ?? 'home');
            $name = strtolower($_GET['name'] ?? '');
          ?>
          <li><a class="<?php echo ($ctl === 'home' ? 'active' : ''); ?>" href="?">Início</a></li>
          <?php if (!$isLogged): ?>
            <!-- Antes do login, apenas Início visível -->
          <?php elseif ($isAtt): ?>
            <li><a class="<?php echo ($ctl==='table' && $name==='dispensacoes' ? 'active' : ''); ?>" href="?controller=table&action=index&name=dispensacoes">Dispensações</a></li>
          <?php else: ?>
            <li><a class="<?php echo ($ctl==='table' && $name==='medicamentos' ? 'active' : ''); ?>" href="?controller=table&action=index&name=medicamentos">Medicamentos</a></li>
            <li><a class="<?php echo ($ctl==='table' && $name==='lotes' ? 'active' : ''); ?>" href="?controller=table&action=index&name=lotes">Lotes</a></li>
            <li><a class="<?php echo ($ctl==='table' && $name==='entradas' ? 'active' : ''); ?>" href="?controller=table&action=index&name=entradas">Entradas</a></li>
            <li><a class="<?php echo ($ctl==='table' && $name==='dispensacoes' ? 'active' : ''); ?>" href="?controller=table&action=index&name=dispensacoes">Dispensações</a></li>
            <li><a class="<?php echo ($ctl==='table' && $name==='laboratorios' ? 'active' : ''); ?>" href="?controller=table&action=index&name=laboratorios">Laboratórios</a></li>
            <li><a class="<?php echo ($ctl==='table' && $name==='classes_terapeuticas' ? 'active' : ''); ?>" href="?controller=table&action=index&name=classes_terapeuticas">Classes terapêuticas</a></li>
            <li><a class="<?php echo ($ctl==='table' && $name==='fornecedores' ? 'active' : ''); ?>" href="?controller=table&action=index&name=fornecedores">Fornecedores</a></li>
            <li><a class="<?php echo ($ctl==='table' && $name==='pacientes' ? 'active' : ''); ?>" href="?controller=table&action=index&name=pacientes">Pacientes</a></li>
            <li><a class="<?php echo ($ctl==='table' && $name==='vw_estoque_por_lote' ? 'active' : ''); ?>" href="?controller=table&action=index&name=vw_estoque_por_lote">Estoque por lote</a></li>
            <li><a class="<?php echo ($ctl==='table' && $name==='vw_estoque_por_medicamento' ? 'active' : ''); ?>" href="?controller=table&action=index&name=vw_estoque_por_medicamento">Estoque por medicamento</a></li>
            <li><a class="<?php echo ($ctl==='table' && $name==='vw_alerta_validade_mes_atual' ? 'active' : ''); ?>" href="?controller=table&action=index&name=vw_alerta_validade_mes_atual">Alertas mês atual</a></li>
            <li><a class="<?php echo ($ctl==='table' && $name==='vw_alerta_validade' ? 'active' : ''); ?>" href="?controller=table&action=index&name=vw_alerta_validade">Alertas de validade</a></li>
            <li><a class="<?php echo ($ctl==='table' && $name==='vw_alerta_estoque_baixo' ? 'active' : ''); ?>" href="?controller=table&action=index&name=vw_alerta_estoque_baixo">Alertas de estoque</a></li>
            <li><a class="<?php echo ($ctl==='table' && $name==='usuarios' ? 'active' : ''); ?>" href="?controller=table&action=index&name=usuarios">Usuários</a></li>
          <?php endif; ?>
        </ul>
      </div>
    </aside>
    <main class="content">
      <?php if (isset($error) && $error) { echo '<div class="error">' . htmlspecialchars($error) . '</div>'; } ?>
      <?php require $viewFile; ?>
    </main>
  </div>

  <script>
    // Dark mode functionality
    const themeToggle = document.getElementById('theme-toggle');
    const themeIcon = themeToggle.querySelector('.theme-icon');
    const themeMenu = document.getElementById('theme-menu');
    const themeOptions = themeMenu ? themeMenu.querySelectorAll('.theme-option') : [];
    const html = document.documentElement;

    function setTheme(theme) {
      const valid = ['light','night','black'];
      const t = valid.includes(theme) ? theme : 'light';
      html.setAttribute('data-theme', t);
      localStorage.setItem('theme', t);
      // Update icon by theme
      let icon = '☀️';
      if (t === 'night') icon = '🌓';
      else if (t === 'black') icon = '🌕';
      themeIcon.textContent = icon;
      // Update active option
      if (themeOptions && themeOptions.length) {
        themeOptions.forEach(btn => {
          if (btn.dataset.theme === t) btn.classList.add('active');
          else btn.classList.remove('active');
        });
      }
    }

    // Check for saved theme preference or default to light
    const savedTheme = localStorage.getItem('theme') || 'light';
    setTheme(savedTheme);

    // Toggle menu visibility
    function toggleThemeMenu() {
      if (!themeMenu) return;
      themeMenu.hidden = !themeMenu.hidden;
    }

    // Open menu on button click
    themeToggle.addEventListener('click', (e) => {
      e.stopPropagation();
      toggleThemeMenu();
    });

    // Handle option selection
    if (themeOptions && themeOptions.length) {
      themeOptions.forEach(btn => btn.addEventListener('click', (e) => {
        e.stopPropagation();
        const t = btn.dataset.theme;
        setTheme(t);
        themeMenu.hidden = true;
      }));
    }

    // Close menu when clicking outside
    document.addEventListener('click', () => {
      if (themeMenu && !themeMenu.hidden) themeMenu.hidden = true;
    });
  </script>
</body>
</html>