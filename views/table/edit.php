<?php
function inputType($dataType) {
    $map = [
        'integer' => 'number', 'bigint' => 'number', 'smallint' => 'number',
        'numeric' => 'number', 'decimal' => 'number', 'double' => 'number', 'real' => 'number', 'float' => 'number',
        'date' => 'date', 'timestamp' => 'datetime-local', 'timestamp without time zone' => 'datetime-local',
        'boolean' => 'checkbox',
        'text' => 'textarea', 'character varying' => 'text', 'varchar' => 'text', 'char' => 'text'
    ];
    $key = strtolower($dataType);
    return $map[$key] ?? 'text';
}
$pkName = $pk ?? null;
?>
<h2>Editar registro em <?php echo htmlspecialchars($table); ?></h2>
<form class="form" method="post">
  <?php foreach ($cols as $col):
      $name = $col['column_name'];
      if ($name === $pkName) continue;
      $type = inputType($col['data_type']);
      $val = $row[$name] ?? '';
      $valEsc = htmlspecialchars((string)$val);
  ?>
  <div class="row">
    <label><?php echo htmlspecialchars($name); ?></label>
    <?php if ($type === 'textarea'): ?>
      <textarea name="<?php echo htmlspecialchars($name); ?>"><?php echo $valEsc; ?></textarea>
    <?php elseif ($type === 'checkbox'): ?>
      <input type="checkbox" name="<?php echo htmlspecialchars($name); ?>" value="1" <?php echo ($val ? 'checked' : ''); ?>>
    <?php else: ?>
      <input type="<?php echo $type; ?>" name="<?php echo htmlspecialchars($name); ?>" value="<?php echo $valEsc; ?>">
    <?php endif; ?>
  </div>
  <?php endforeach; ?>
  <button class="btn btn-primary" type="submit">Salvar</button>
  <a class="btn btn-outline" href="?controller=table&action=index&name=<?php echo urlencode($table); ?>">Cancelar</a>
</form>