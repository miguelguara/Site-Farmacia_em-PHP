<?php
$table = $name ?? ($table ?? '');
$pk = $pk ?? null;
$totalPages = max(1, (int)ceil($total / $perPage));
function prettyLabel($s) {
  $s = (string)$s;
  if (preg_match('/_id$/', $s)) { $s = substr($s, 0, -3); }
  $s = preg_replace('/_+/', ' ', $s);
  return trim($s);
}
?>
<div class="table-title">
  <h2 style="margin: 0; font-size: 20px; font-weight: 700;"><?php echo htmlspecialchars($table); ?></h2>
  <?php if (empty($isView) || $isView === false): ?>
  <a class="btn btn-primary" href="?controller=table&action=create&name=<?php echo urlencode($table); ?>">Novo Registro</a>
  <?php endif; ?>
</div>
<div class="table-wrapper">
<table class="table">
  <thead>
    <tr>
      <?php foreach ($columns as $c): ?>
        <th><?php echo htmlspecialchars(prettyLabel($c)); ?></th>
      <?php endforeach; ?>
      <th style="width:200px">Ações</th>
    </tr>
  </thead>
  <tbody>
    <?php if (empty($rows)): ?>
      <tr>
        <td colspan="<?php echo count($columns) + 1; ?>" style="text-align: center; padding: 40px 20px; color: var(--text-secondary);">
          <div style="font-size: 48px; margin-bottom: 16px;">📭</div>
          <div style="font-size: 16px; font-weight: 600; margin-bottom: 8px;">Nenhum registro encontrado</div>
          <div style="font-size: 14px;">Comece adicionando um novo registro usando o botão acima.</div>
        </td>
      </tr>
    <?php else: ?>
      <?php foreach ($rows as $row): ?>
        <tr>
          <?php foreach ($columns as $c): ?>
            <?php
              $val = $row[$c] ?? '';
              $labelMaps = $fkLabelMaps ?? [];
              $display = $val;
              if (isset($labelMaps[$c])) {
                $key = (string)$val;
                if ($key !== '' && isset($labelMaps[$c][$key])) {
                  $display = $labelMaps[$c][$key];
                }
              }
            ?>
            <td><?php echo htmlspecialchars((string)$display); ?></td>
          <?php endforeach; ?>
          <td class="actions">
            <?php $id = $pk ? ($row[$pk] ?? null) : null; ?>
            <?php if ($id !== null): ?>
              <a class="btn btn-outline" href="?controller=table&action=view&name=<?php echo urlencode($table); ?>&id=<?php echo urlencode($id); ?>">Ver</a>
              <a class="btn btn-outline" href="?controller=table&action=edit&name=<?php echo urlencode($table); ?>&id=<?php echo urlencode($id); ?>">Editar</a>
              <a class="btn btn-danger" href="?controller=table&action=delete&name=<?php echo urlencode($table); ?>&id=<?php echo urlencode($id); ?>" onclick="return confirm('Excluir registro?');">Excluir</a>
            <?php else: ?>
              —
            <?php endif; ?>
          </td>
        </tr>
      <?php endforeach; ?>
    <?php endif; ?>
  </tbody>
</table>
</div>
<div class="table-bottom-scroll" id="tbs"><div class="spacer"></div></div>
<script>
const tw=document.querySelector('.table-wrapper');
const tbs=document.getElementById('tbs');
const sp=tbs?tbs.querySelector('.spacer'):null;
function w(){ if(tw&&sp){ sp.style.width=tw.scrollWidth+'px'; } }
function s(){ if(tw&&tbs){ tw.scrollLeft=tbs.scrollLeft; } }
function r(){ if(tw&&tbs){ tbs.scrollLeft=tw.scrollLeft; } }
window.addEventListener('resize',w);
w();
if(tbs){ tbs.addEventListener('scroll',s); }
if(tw){ tw.addEventListener('scroll',r); }
</script>
<div class="pagination">
  <span>Página <?php echo $page; ?> de <?php echo $totalPages; ?>.</span>
  <?php if ($page > 1): ?>
    <a href="?controller=table&action=index&name=<?php echo urlencode($table); ?>&page=<?php echo $page - 1; ?>">Anterior</a>
  <?php endif; ?>
  <?php if ($page < $totalPages): ?>
    <a href="?controller=table&action=index&name=<?php echo urlencode($table); ?>&page=<?php echo $page + 1; ?>">Próxima</a>
  <?php endif; ?>
</div>