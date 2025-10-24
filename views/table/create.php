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
?>
<h2>Novo registro em <?php echo htmlspecialchars($table); ?></h2>
<form class="form" method="post">
  <?php foreach ($cols as $col):
      $name = $col['column_name'];
      $isAuto = (Database::driver() === 'pgsql')
        ? (!empty($col['column_default']) && str_contains($col['column_default'], 'nextval('))
        : (isset($col['EXTRA']) && str_contains(strtolower($col['EXTRA']), 'auto_increment'));
      if ($isAuto) continue;
      $type = inputType($col['data_type']);
      $isFk = isset($fkOptions) && isset($fkOptions[$name]);
      $isEnum = isset($enumOptions) && isset($enumOptions[$name]);
      $nullable = strtolower($col['is_nullable'] ?? '') === 'yes';
  ?>
  <div class="row">
    <label><?php echo htmlspecialchars($name); ?></label>
    <?php if ($isFk || $isEnum): ?>
      <?php $options = $isFk ? ($fkOptions[$name] ?? []) : ($enumOptions[$name] ?? []); ?>
      <select name="<?php echo htmlspecialchars($name); ?>" <?php echo $nullable ? '' : 'required'; ?>>
        <option value="" <?php echo $nullable ? '' : 'selected'; ?>>— selecione —</option>
        <?php foreach ($options as $opt): ?>
          <option value="<?php echo htmlspecialchars((string)$opt['value']); ?>">
            <?php echo htmlspecialchars($opt['label']); ?>
          </option>
        <?php endforeach; ?>
      </select>
    <?php elseif ($table === 'usuarios' && $name === 'login'): ?>
      <div class="radio-group">
        <label><input type="radio" name="login" value="admin" required> admin</label>
        <label><input type="radio" name="login" value="farma" required> farma</label>
        <label><input type="radio" name="login" value="atendente" required> atendente</label>
      </div>
    <?php elseif ($type === 'textarea'): ?>
      <textarea name="<?php echo htmlspecialchars($name); ?>"></textarea>
    <?php elseif ($type === 'checkbox'): ?>
      <input type="checkbox" name="<?php echo htmlspecialchars($name); ?>" value="1">
    <?php else: ?>
      <input type="<?php echo $type; ?>" name="<?php echo htmlspecialchars($name); ?>">
    <?php endif; ?>
  </div>
  <?php endforeach; ?>
  <button class="btn btn-primary" type="submit">Salvar</button>
  <a class="btn btn-outline" href="?controller=table&action=index&name=<?php echo urlencode($table); ?>">Cancelar</a>
</form>