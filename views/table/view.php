<?php
?>
<h2>Visualizar registro em <?php echo htmlspecialchars($table); ?></h2>
<table class="table">
  <tbody>
    <?php foreach ($row as $key => $value): ?>
      <tr>
        <th><?php echo htmlspecialchars(preg_replace('/_+/', ' ', (string)$key)); ?></th>
        <td><?php echo htmlspecialchars((string)$value); ?></td>
      </tr>
    <?php endforeach; ?>
  </tbody>
</table>
<p class="mt-1">
  <a class="btn btn-outline" href="?controller=table&action=index&name=<?php echo urlencode($table); ?>">Voltar</a>
</p>