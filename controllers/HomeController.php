<?php
require_once __DIR__ . '/../core/Database.php';

class HomeController {
    public function index() {
        try {
            $pdo = Database::getConnection();
        } catch (\Throwable $e) {
            $error = 'Erro de conexão: ' . $e->getMessage();
            return $this->render('home/index', ['tables' => [], 'error' => $error]);
        }
        $driver = Database::driver();
        $schema = Database::schema();

        if ($driver === 'pgsql') {
            $sql = "SELECT table_name FROM information_schema.tables
                    WHERE table_schema = :schema AND table_type='BASE TABLE'
                    ORDER BY table_name";
            $stmt = $pdo->prepare($sql);
            $stmt->execute(['schema' => $schema]);
            $tables = $stmt->fetchAll();
        } else {
            $sql = "SELECT TABLE_NAME AS table_name FROM information_schema.tables
                    WHERE table_schema = DATABASE() AND TABLE_TYPE='BASE TABLE'
                    ORDER BY TABLE_NAME";
            $stmt = $pdo->query($sql);
            $tables = $stmt->fetchAll();
        }

        $this->render('home/index', ['tables' => $tables]);
    }

    private function render(string $view, array $data = []) {
        extract($data);
        $viewFile = __DIR__ . '/../views/' . $view . '.php';
        $layout = __DIR__ . '/../views/layout.php';
        require $layout;
    }
}