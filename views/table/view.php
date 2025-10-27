<?php
?>
<h2>Visualizar registro em <?php echo htmlspecialchars($table); ?></h2>
<table class="table">
  <tbody>
    <?php
      function prettyLabel($s) {
        $s = (string)$s;
        if (preg_match('/_id$/', $s)) { $s = substr($s, 0, -3); }
        $s = preg_replace('/_+/', ' ', $s);
        return trim($s);
      }
    ?>
    <?php foreach ($row as $key => $value): ?>
      <tr>
        <th><?php echo htmlspecialchars(prettyLabel($key)); ?></th>
        <td><?php echo htmlspecialchars((string)$value); ?></td>
      </tr>
    <?php endforeach; ?>
  </tbody>
</table>
<p class="mt-1">
  <a class="btn btn-outline" href="?controller=table&action=index&name=<?php echo urlencode($table); ?>">Voltar</a>
</p>