<?php
require_once __DIR__ . '/../models/TableModel.php';

class TableController {
    private function render(string $view, array $data = []) {
        extract($data);
        $viewFile = __DIR__ . '/../views/' . $view . '.php';
        $layout = __DIR__ . '/../views/layout.php';
        require $layout;
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
        $this->render('table/create', ['table' => $name, 'cols' => $cols]);
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
        $this->render('table/edit', ['table' => $name, 'row' => $row, 'cols' => $cols, 'pk' => $pk]);
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