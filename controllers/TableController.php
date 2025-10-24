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
        $rows = $m->all(200, 0);
        $opts = [];
        foreach ($rows as $r) {
            $id = $r['id'] ?? null;
            if ($id === null) continue;
            $label = (string)($r[$labelCol] ?? ('#' . $id));
            $opts[] = ['value' => $id, 'label' => $label];
        }
        return $opts;
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

    public function index() {
        $name = $_GET['name'] ?? null;
        if (!$name) { http_response_code(400); echo 'Falta o nome da tabela'; return; }
        $model = new TableModel($name);
        $page = max(1, (int)($_GET['page'] ?? 1));
        $perPage = 20;
        $offset = ($page - 1) * $perPage;
        $rows = $model->all($perPage, $offset);
        $total = $model->count();
        $columns = array_map(fn($c) => $c['column_name'], $model->columns());
        $pk = $model->getPrimaryKey();
        $isView = $model->isView();
        $this->render('table/index', compact('name', 'rows', 'columns', 'page', 'perPage', 'total', 'pk', 'isView'));
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
        $this->render('table/create', ['table' => $name, 'cols' => $cols, 'fkOptions' => $fkOptions]);
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
        $this->render('table/edit', ['table' => $name, 'row' => $row, 'cols' => $cols, 'pk' => $pk, 'fkOptions' => $fkOptions]);
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