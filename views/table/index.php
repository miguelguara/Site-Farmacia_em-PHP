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
<h2>Tabela: <?php echo htmlspecialchars($table); ?></h2>
<?php if (empty($isView) || $isView === false): ?>
<p>
  <a class="btn btn-primary" href="?controller=table&action=create&name=<?php echo urlencode($table); ?>">Novo</a>
</p>
<?php endif; ?>
<table class="table">
  <thead>
    <tr>
      <?php foreach ($columns as $c): ?>
        <th><?php echo htmlspecialchars(prettyLabel($c)); ?></th>
      <?php endforeach; ?>
      <th style="width:160px">Ações</th>
    </tr>
  </thead>
  <tbody>
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
  </tbody>
</table>
<div class="pagination">
  <span>Página <?php echo $page; ?> de <?php echo $totalPages; ?>.</span>
  <?php if ($page > 1): ?>
    <a href="?controller=table&action=index&name=<?php echo urlencode($table); ?>&page=<?php echo $page - 1; ?>">Anterior</a>
  <?php endif; ?>
  <?php if ($page < $totalPages): ?>
    <a href="?controller=table&action=index&name=<?php echo urlencode($table); ?>&page=<?php echo $page + 1; ?>">Próxima</a>
  <?php endif; ?>
</div>