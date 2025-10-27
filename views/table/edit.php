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
function prettyLabel($s) {
    return preg_replace('/_+/', ' ', (string)$s);
}
?>
<h2>Editar registro em <?php echo htmlspecialchars($table); ?></h2>
<form class="form" method="post">
  <?php foreach ($cols as $col):
      $name = $col['column_name'];
      if ($name === $pkName) continue;
      $type = inputType($col['data_type']);
      $val = $row[$name] ?? '';
      $valEsc = htmlspecialchars((string)$val);
      $isFk = isset($fkOptions) && isset($fkOptions[$name]);
      $isEnum = isset($enumOptions) && isset($enumOptions[$name]);
      $nullable = strtolower($col['is_nullable'] ?? '') === 'yes';
  ?>
  <div class="row">
    <label><?php echo htmlspecialchars(prettyLabel($name)); ?></label>
    <?php if ($isFk || $isEnum): ?>
      <?php $options = $isFk ? ($fkOptions[$name] ?? []) : ($enumOptions[$name] ?? []); ?>
      <select name="<?php echo htmlspecialchars($name); ?>">
        <?php if ($nullable): ?><option value="">— selecione —</option><?php endif; ?>
        <?php foreach ($options as $opt): ?>
          <?php $selected = ((string)$opt['value'] === (string)$val) ? 'selected' : ''; ?>
          <option value="<?php echo htmlspecialchars((string)$opt['value']); ?>" <?php echo $selected; ?>>
            <?php echo htmlspecialchars($opt['label']); ?>
          </option>
        <?php endforeach; ?>
      </select>
    <?php elseif ($table === 'usuarios' && $name === 'login'): ?>
      <div class="radio-group">
        <label><input type="radio" name="login" value="admin" <?php echo ($val === 'admin' ? 'checked' : ''); ?>> admin</label>
        <label><input type="radio" name="login" value="farma" <?php echo ($val === 'farma' ? 'checked' : ''); ?>> farma</label>
        <label><input type="radio" name="login" value="atendente" <?php echo ($val === 'atendente' ? 'checked' : ''); ?>> atendente</label>
      </div>
    <?php elseif ($type === 'textarea'): ?>
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