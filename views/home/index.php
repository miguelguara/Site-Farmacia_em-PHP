<?php
// Home dashboard with responsive charts: stock bar chart + weekly pies (entries/exits)
// This view queries the DB directly to assemble chart data, then renders pure HTML/CSS.

// Fetch data using the existing Database helper
try {
    $pdo = Database::getConnection();

    // 1) Stock by medicine (uses schema view for consistent conversion to base units)
    $stmtStock = $pdo->query(
        "SELECT nome, codigo, unidade_base, quantidade_disponivel\n" .
        "FROM vw_estoque_por_medicamento\n" .
        "ORDER BY nome"
    );
    $stockRows = $stmtStock->fetchAll();

    // Build associative arrays for quick lookups
    $stockByMed = [];
    $maxStock = 0;
    $totalStock = 0;
    foreach ($stockRows as $row) {
        $name = $row['nome'];
        $qty = (float)($row['quantidade_disponivel'] ?? 0);
        $stockByMed[$name] = $qty;
        if ($qty > $maxStock) $maxStock = $qty;
        $totalStock += $qty;
    }

    // 2) Weekly entries per medicine (last 7 days)
    $stmtEntries = $pdo->query(
        "SELECT m.nome, SUM(e.quantidade_base) AS total\n" .
        "FROM entradas e\n" .
        "JOIN lotes l ON l.id = e.lote_id\n" .
        "JOIN medicamentos m ON m.id = l.medicamento_id\n" .
        "WHERE e.data_entrada >= CURRENT_DATE - INTERVAL '6 days'\n" .
        "GROUP BY m.nome\n" .
        "ORDER BY m.nome"
    );
    $entriesRows = $stmtEntries->fetchAll();
    $entriesByMed = [];
    $entriesTotal = 0.0;
    foreach ($entriesRows as $row) {
        $name = $row['nome'];
        $qty = (float)$row['total'];
        $entriesByMed[$name] = $qty;
        $entriesTotal += $qty;
    }

    // 3) Weekly exits per medicine (last 7 days)
    $stmtExits = $pdo->query(
        "SELECT m.nome, SUM(d.quantidade_base) AS total\n" .
        "FROM dispensacoes d\n" .
        "JOIN lotes l ON l.id = d.lote_id\n" .
        "JOIN medicamentos m ON m.id = l.medicamento_id\n" .
        "WHERE date(d.data_dispensa) >= CURRENT_DATE - INTERVAL '6 days'\n" .
        "GROUP BY m.nome\n" .
        "ORDER BY m.nome"
    );
    $exitsRows = $stmtExits->fetchAll();
    $exitsByMed = [];
    $exitsTotal = 0.0;
    foreach ($exitsRows as $row) {
        $name = $row['nome'];
        $qty = (float)$row['total'];
        $exitsByMed[$name] = $qty;
        $exitsTotal += $qty;
    }

    // Unified ordered list of medicines for consistent color mapping across charts
    $meds = array_keys($stockByMed);
    foreach (array_keys($entriesByMed) as $n) if (!in_array($n, $meds, true)) $meds[] = $n;
    foreach (array_keys($exitsByMed) as $n) if (!in_array($n, $meds, true)) $meds[] = $n;
    sort($meds);

} catch (Throwable $e) {
    $errorMsg = $e->getMessage();
    $stockRows = [];
    $stockByMed = [];
    $entriesByMed = [];
    $exitsByMed = [];
    $meds = [];
    $maxStock = 0;
    $entriesTotal = 0;
    $exitsTotal = 0;
}
?>

<style>
  .home-dashboard { display: grid; gap: 1.25rem; }
  .home-dashboard__section { background: var(--bg-secondary); border: 1px solid var(--border-color); border-radius: 10px; padding: 1rem; }

  /* Horizontal bar chart */
  .chart-hbar { width: 100%; min-height: 420px; display: flex; flex-direction: column; gap: 10px; justify-content: center; margin: 0 auto; }
  .chart-hbar__row { display: grid; grid-template-columns: minmax(140px, 240px) 1fr auto; align-items: center; gap: 12px; }
  .chart-hbar__label { font-size: 0.9rem; color: var(--text, #333); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
  .chart-hbar__bar { position: relative; height: 22px; background: var(--bg-tertiary); border: 1px solid var(--border-color); border-radius: 6px; }
  .chart-hbar__fill { position: absolute; left: 0; top: 0; bottom: 0; width: var(--bar-w, 0%); background: var(--bar-color, var(--button-primary)); border-radius: 6px; }
  .chart-hbar__value { font-size: 0.85rem; color: var(--text-secondary); text-align: right; min-width: 120px; }
  .chart-bar__legend { display: flex; flex-wrap: wrap; gap: 8px 14px; margin-top: 8px; font-size: 0.8rem; }
  .legend-item { display: inline-flex; align-items: center; gap: 6px; }
  .legend-swatch { width: 14px; height: 14px; border-radius: 50%; border: 1px solid var(--border, #ccc); }

  /* Pie charts */
  .pies { display: grid; grid-template-columns: repeat(2, minmax(220px, 1fr)); gap: 1rem; align-items: start; }
  .pie-card { display: grid; grid-template-rows: auto auto auto; gap: 0.75rem; justify-items: center; }
  .pie { width: clamp(160px, 45%, 260px); aspect-ratio: 1; border-radius: 50%; border: 1px solid var(--border-color); background: conic-gradient(var(--bg-tertiary) 0deg, var(--bg-tertiary) 360deg); margin: 0 auto; }
  .pie-legend { display: flex; flex-wrap: wrap; gap: 8px 14px; }

  @media (max-width: 900px) { .chart-bar__bar { min-width: 42px; } }
  @media (max-width: 700px) {
    .pies { grid-template-columns: 1fr; }
    .chart-bar__bar .bar::after { font-size: 0.75rem; top: -22px; }
  }
</style>

<div class="home-dashboard">
  <section class="home-dashboard__section">
    <div class="hero" style="margin-bottom:0.5rem;">Estoque por medicamento</div>
    <?php if (isset($errorMsg)): ?>
      <div style="color: #c92a2a;">Erro ao carregar dados: <?= htmlspecialchars($errorMsg) ?></div>
    <?php endif; ?>

    <div class="chart-hbar" aria-label="Gráfico de barras horizontais de estoque disponível">
      <?php
        // Paleta usada nas pizzas para manter correspondência de cores
        $palette = [
          '#4c6ef5','#22b8cf','#51cf66','#fcc419','#ff922b','#ff6b6b','#845ef7','#64dfdf','#94d82d','#ffd43b',
          '#ff8a65','#66bb6a','#29b6f6','#ab47bc','#ffa000','#26a69a'
        ];

        // Ordena para o gráfico por estoque (desc), mas mantém índice de cor pelo array $meds
        $medsDesc = $meds;
        usort($medsDesc, function($a, $b) use ($stockByMed) {
          $va = isset($stockByMed[$a]) ? (float)$stockByMed[$a] : 0.0;
          $vb = isset($stockByMed[$b]) ? (float)$stockByMed[$b] : 0.0;
          if ($va === $vb) return strcasecmp($a, $b);
          return ($vb <=> $va); // descending
        });

        if (!empty($medsDesc)) {
          foreach ($medsDesc as $med) {
            $value = isset($stockByMed[$med]) ? (float)$stockByMed[$med] : 0.0;
            $width = ($maxStock > 0) ? max(0.0, min(100.0, ($value / $maxStock) * 100.0)) : 0.0;
            $share = ($totalStock > 0) ? ($value / $totalStock * 100.0) : 0.0;
            // A cor vem da posição do medicamento em $meds (mesmo mapeamento das pizzas)
            $idx = array_search($med, $meds, true);
            $color = $palette[$idx % count($palette)];
            ?>
            <div class="chart-hbar__row" title="<?= htmlspecialchars($med) ?>">
              <div class="chart-hbar__label"><?= htmlspecialchars($med) ?></div>
              <div class="chart-hbar__bar">
                <div class="chart-hbar__fill" style="--bar-color: <?= $color ?>; background: <?= $color ?>; --bar-w: <?= number_format($width, 2, '.', '') ?>%;"></div>
              </div>
              <div class="chart-hbar__value"><?= number_format($value, 0, ',', '.') ?> (<?= number_format($share, 1, ',', '.') ?>%)</div>
            </div>
            <?php
          }
        } else {
          echo '<div style="color:#555;">Sem dados de estoque.</div>';
        }
      ?>
      <?php /* Legenda removida por ser redundante com os rótulos das barras */ ?>
    </div>
  </section>

  <section class="home-dashboard__section">
    <h2 style="margin:0 0 0.5rem 0;">Movimentação na semana</h2>
    <div class="pies">
      <?php
      // Helper to build conic-gradient string given data and palette
      $buildPieGradient = function(array $valuesByMed, array $allMeds, array $palette) {
          $total = 0.0;
          foreach ($valuesByMed as $v) { $total += (float)$v; }
          if ($total <= 0) {
              return 'conic-gradient(var(--bg-tertiary) 0deg, var(--bg-tertiary) 360deg)';
          }
          $start = 0.0;
          $segments = [];
          $count = count($allMeds);
          $i = 0;
          foreach ($allMeds as $med) {
              $val = isset($valuesByMed[$med]) ? (float)$valuesByMed[$med] : 0.0;
              $deg = ($val / $total) * 360.0;
              $color = $palette[$i % count($palette)];
              $end = $start + $deg;
              $segments[] = sprintf('%s %.3fdeg %.3fdeg', $color, $start, $end);
              $start = $end;
              $i++;
          }
          // Fix rounding drift: ensure last end reaches 360deg
          if (!empty($segments)) {
              $lastColor = $palette[min($count-1, count($palette)-1)];
              $endSum = 0.0;
              foreach ($segments as $seg) {
                  // parse end value from segment string (approx)
                  if (preg_match('/ ([0-9]+\.[0-9]+|[0-9]+)deg ([0-9]+\.[0-9]+|[0-9]+)deg$/', $seg, $m)) {
                      $endSum = (float)$m[2];
                  }
              }
              if ($endSum < 360.0) {
                  $segments[count($segments)-1] = preg_replace('/deg [0-9]+\.?[0-9]*deg$/', 'deg 360deg', $segments[count($segments)-1]);
              }
          }
          return 'conic-gradient(' . implode(', ', $segments) . ')';
      };
      ?>

      <!-- Pie: Saídas -->
      <div class="pie-card" aria-label="Gráfico pizza de saídas na semana">
        <h3 style="margin:0; font-size:1rem;">Saída de remédios (7 dias)</h3>
        <?php $pieExitsBg = $buildPieGradient($exitsByMed, $meds, $palette); ?>
        <div class="pie" style="background: <?= $pieExitsBg ?>;"></div>
        <div class="pie-legend">
          <?php $i = 0; foreach ($meds as $med): $color = $palette[$i % count($palette)]; $val = isset($exitsByMed[$med]) ? (float)$exitsByMed[$med] : 0.0; $pct = ($exitsTotal > 0 ? ($val / $exitsTotal * 100.0) : 0.0); ?>
            <span class="legend-item" title="<?= htmlspecialchars($med) ?>: <?= number_format($val, 0, ',', '.') ?>">
              <span class="legend-swatch" style="background: <?= $color ?>;"></span>
              <?= htmlspecialchars($med) ?> (<?= number_format($val, 0, ',', '.') ?> | <?= number_format($pct, 1, ',', '.') ?>%)
            </span>
          <?php $i++; endforeach; ?>
          <?php if (empty($meds)) echo '<span style="color:#555;">Sem dados de saída.</span>'; ?>
        </div>
      </div>

      <!-- Pie: Entradas -->
      <div class="pie-card" aria-label="Gráfico pizza de entradas na semana">
        <h3 style="margin:0; font-size:1rem;">Entrada de remédios (7 dias)</h3>
        <?php $pieEntriesBg = $buildPieGradient($entriesByMed, $meds, $palette); ?>
        <div class="pie" style="background: <?= $pieEntriesBg ?>;"></div>
        <div class="pie-legend">
          <?php $i = 0; foreach ($meds as $med): $color = $palette[$i % count($palette)]; $val = isset($entriesByMed[$med]) ? (float)$entriesByMed[$med] : 0.0; $pct = ($entriesTotal > 0 ? ($val / $entriesTotal * 100.0) : 0.0); ?>
            <span class="legend-item" title="<?= htmlspecialchars($med) ?>: <?= number_format($val, 0, ',', '.') ?>">
              <span class="legend-swatch" style="background: <?= $color ?>;"></span>
              <?= htmlspecialchars($med) ?> (<?= number_format($val, 0, ',', '.') ?> | <?= number_format($pct, 1, ',', '.') ?>%)
            </span>
          <?php $i++; endforeach; ?>
          <?php if (empty($meds)) echo '<span style="color:#555;">Sem dados de entrada.</span>'; ?>
        </div>
      </div>
    </div>
  </section>
</div>
$driver = Database::driver();
$schema = Database::schema();
?>
<div class="card">
  <div class="hero">Bem-vindo ao Sistema de Gestão de Farmácia</div>
  <p class="sub">Escolha uma opção no menu acima para começar.</p>

  <?php $loginStr = strtolower($_SESSION['user']['login'] ?? ''); $isAtt = strpos($loginStr, 'atendente') !== false; ?>
  <div class="grid mt-2">
    <?php if ($isAtt): ?>
      <div class="tile">
        <div class="icon">➡️</div>
        <h3>Dispensações</h3>
        <p>Registrar dispensação de medicamentos</p>
        <a class="btn btn-primary" href="?controller=table&action=index&name=dispensacoes">Acessar</a>
      </div>
    <?php else: ?>
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
        <a class="btn btn-primary" href="?controller=table&action=index&name=dispensacoes">Acessar</a>
      </div>
      <div class="tile">
        <div class="icon">🏭</div>
        <h3>Laboratórios</h3>
        <p>Cadastro de laboratórios</p>
        <a class="btn btn-primary" href="?controller=table&action=index&name=laboratorios">Acessar</a>
      </div>
      <div class="tile">
        <div class="icon">🧪</div>
        <h3>Classes terapêuticas</h3>
        <p>Cadastro de classes</p>
        <a class="btn btn-primary" href="?controller=table&action=index&name=classes_terapeuticas">Acessar</a>
      </div>
      <div class="tile">
        <div class="icon">🚚</div>
        <h3>Fornecedores</h3>
        <p>Cadastro de fornecedores</p>
        <a class="btn btn-primary" href="?controller=table&action=index&name=fornecedores">Acessar</a>
      </div>
      <div class="tile">
        <div class="icon">📦</div>
        <h3>Estoque por lote</h3>
        <p>Consulta de estoque por lote (somente leitura)</p>
        <a class="btn btn-primary" href="?controller=table&action=index&name=vw_estoque_por_lote">Acessar</a>
      </div>
      <div class="tile">
        <div class="icon">🧴</div>
        <h3>Estoque por medicamento</h3>
        <p>Consulta de estoque por medicamento (somente leitura)</p>
        <a class="btn btn-primary" href="?controller=table&action=index&name=vw_estoque_por_medicamento">Acessar</a>
      </div>
      <div class="tile">
        <div class="icon">⏳</div>
        <h3>Alertas de validade</h3>
        <p>Vencidos e próximos de vencer</p>
        <a class="btn btn-primary" href="?controller=table&action=index&name=vw_alerta_validade">Acessar</a>
      </div>
      <div class="tile">
        <div class="icon">📅</div>
        <h3>Alertas do mês</h3>
        <p>Lotes que vencem no mês atual</p>
        <a class="btn btn-primary" href="?controller=table&action=index&name=vw_alerta_validade_mes_atual">Acessar</a>
      </div>
      <div class="tile">
        <div class="icon">⚠️</div>
        <h3>Alertas de estoque</h3>
        <p>Estoque mínimo/≤10 unidades/≤20%</p>
        <a class="btn btn-primary" href="?controller=table&action=index&name=vw_alerta_estoque_baixo">Acessar</a>
      </div>
      <div class="tile">
        <div class="icon">👥</div>
        <h3>Pacientes</h3>
        <p>Gerenciar cadastro de pacientes</p>
        <a class="btn btn-primary" href="?controller=table&action=index&name=pacientes">Acessar</a>
      </div>
    <?php endif; ?>
  </div>

  <div class="mt-2">
    <p style="color:#6b7280;">Driver: <strong><?php echo htmlspecialchars($driver); ?></strong> | Schema: <strong><?php echo htmlspecialchars($schema ?? 'database atual'); ?></strong></p>
  </div>
</div>