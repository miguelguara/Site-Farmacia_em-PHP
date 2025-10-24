<?php
require_once __DIR__ . '/../models/TableModel.php';

class TableController {
    private function render(string $view, array $data = []) {
        extract($data);
        $viewFile = __DIR__ . '/../views/' . $view . '.php';
        $layout = __DIR__ . '/../views/layout.php';
        require $layout;
    }

    private function guessFkTargetTable(string $colName): ?string {
        // Mapeamento explícito para cobrir casos comuns
        $map = [
            'laboratorio_id' => 'laboratorios',
            'classe_terapeutica_id' => 'classes_terapeuticas',
            'medicamento_id' => 'medicamentos',
            'fornecedor_id' => 'fornecedores',
            'paciente_id' => 'pacientes',
            'lote_id' => 'lotes',
            'usuario_id' => 'usuarios',
        ];
        if (isset($map[$colName])) return $map[$colName];
        // Heurística simples: remove _id e pluraliza com 's'
        if (str_ends_with($colName, '_id')) {
            $base = substr($colName, 0, -3);
            return $base . 's';
        }
        return null;
    }

    private function pickLabelColumn(TableModel $model): string {
        $cols = array_map(fn($c) => strtolower($c['column_name']), $model->columns());
        foreach (['nome','descricao','codigo','label','titulo'] as $pref) {
            if (in_array($pref, $cols, true)) return $pref;
        }
        return in_array('id', $cols, true) ? 'id' : ($cols[0] ?? 'id');
    }

    private function optionsForTable(string $table): array {
        $m = new TableModel($table);
        $labelCol = $this->pickLabelColumn($m);
        $rows = $m->all(1000, 0);
        $opts = [];
        foreach ($rows as $r) {
            $id = $r['id'] ?? null;
            if ($id === null) continue;
            $label = (string)($r[$labelCol] ?? ('#' . $id));
            $opts[] = ['value' => $id, 'label' => $label];
        }
        return $opts;
    }

    private function optionsAssocForTable(string $table): array {
        $assoc = [];
        foreach ($this->optionsForTable($table) as $opt) {
            $assoc[(string)$opt['value']] = $opt['label'];
        }
        return $assoc;
    }

    private function buildFkOptions(array $cols): array {
        $result = [];
        foreach ($cols as $col) {
            $name = $col['column_name'];
            $target = $this->guessFkTargetTable($name);
            if ($target) {
                try {
                    $result[$name] = $this->optionsForTable($target);
                } catch (\Throwable $e) {
                    // Silencia em caso de tabela inexistente
                }
            }
        }
        return $result;
    }

    private function enumOptionsForUdt(string $udt): array {
        if (Database::driver() !== 'pgsql') return [];
        $pdo = Database::getConnection();
        $sql = "SELECT e.enumlabel FROM pg_type t JOIN pg_enum e ON e.enumtypid = t.oid WHERE t.typname = :udt ORDER BY e.enumsortorder";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['udt' => $udt]);
        $rows = $stmt->fetchAll();
        return array_map(fn($r) => (string)$r['enumlabel'], $rows);
    }

    private function buildEnumOptions(array $cols): array {
        $result = [];
        foreach ($cols as $col) {
            $name = $col['column_name'];
            $dataType = strtolower($col['data_type'] ?? '');
            $udt = $col['udt_name'] ?? null;
            if (Database::driver() === 'pgsql' && $dataType === 'user-defined' && $udt) {
                $labels = $this->enumOptionsForUdt($udt);
                if ($labels) {
                    $result[$name] = array_map(fn($label) => ['value' => $label, 'label' => $label], $labels);
                }
            }
        }
        return $result;
    }

    private function buildFkLabelMaps(array $cols): array {
        $maps = [];
        foreach ($cols as $col) {
            $name = $col['column_name'];
            $target = $this->guessFkTargetTable($name);
            if ($target) {
                try {
                    $maps[$name] = $this->optionsAssocForTable($target);
                } catch (\Throwable $e) {
                    // ignora
                }
            }
        }
        return $maps;
    }

    public function index() {
        $name = $_GET['name'] ?? null;
        if (!$name) { http_response_code(400); echo 'Falta o nome da tabela'; return; }
        $model = new TableModel($name);
        $page = max(1, (int)($_GET['page'] ?? 1));
        $perPage = 20;
        $offset = ($page - 1) * $perPage;
        $rows = $model->all($perPage, $offset);
        $total = $model->count();
        $cols = $model->columns();
        $columns = array_map(fn($c) => $c['column_name'], $cols);
        $pk = $model->getPrimaryKey();
        $isView = $model->isView();
        $fkLabelMaps = $this->buildFkLabelMaps($cols);
        $this->render('table/index', compact('name', 'rows', 'columns', 'page', 'perPage', 'total', 'pk', 'isView', 'fkLabelMaps'));
    }

    public function create() {
        $name = $_GET['name'] ?? null;
        if (!$name) { http_response_code(400); echo 'Falta o nome da tabela'; return; }
        $model = new TableModel($name);
        if ($model->isView()) { http_response_code(405); echo 'Esta visão é somente leitura.'; return; }
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $model->create($_POST);
            header('Location: ?controller=table&action=index&name=' . urlencode($name));
            return;
        }
        $cols = $model->columns();
        $fkOptions = $this->buildFkOptions($cols);
        $enumOptions = $this->buildEnumOptions($cols);
        $this->render('table/create', ['table' => $name, 'cols' => $cols, 'fkOptions' => $fkOptions, 'enumOptions' => $enumOptions]);
    }

    public function edit() {
        $name = $_GET['name'] ?? null;
        $id   = $_GET['id'] ?? null;
        if (!$name || $id === null) { http_response_code(400); echo 'Parâmetros insuficientes'; return; }
        $model = new TableModel($name);
        if ($model->isView()) { http_response_code(405); echo 'Esta visão é somente leitura.'; return; }
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $model->update($id, $_POST);
            header('Location: ?controller=table&action=index&name=' . urlencode($name));
            return;
        }
        $row = $model->find($id);
        $cols = $model->columns();
        $pk = $model->getPrimaryKey();
        $fkOptions = $this->buildFkOptions($cols);
        $enumOptions = $this->buildEnumOptions($cols);
        $this->render('table/edit', ['table' => $name, 'row' => $row, 'cols' => $cols, 'pk' => $pk, 'fkOptions' => $fkOptions, 'enumOptions' => $enumOptions]);
    }

    public function delete() {
        $name = $_GET['name'] ?? null;
        $id   = $_GET['id'] ?? null;
        if (!$name || $id === null) { http_response_code(400); echo 'Parâmetros insuficientes'; return; }
        $model = new TableModel($name);
        if ($model->isView()) { http_response_code(405); echo 'Esta visão é somente leitura.'; return; }
        $model->delete($id);
        header('Location: ?controller=table&action=index&name=' . urlencode($name));
    }

    public function view() {
        $name = $_GET['name'] ?? null;
        $id   = $_GET['id'] ?? null;
        if (!$name || $id === null) { http_response_code(400); echo 'Parâmetros insuficientes'; return; }
        $model = new TableModel($name);
        $row = $model->find($id);
        $this->render('table/view', ['table' => $name, 'row' => $row]);
    }
}