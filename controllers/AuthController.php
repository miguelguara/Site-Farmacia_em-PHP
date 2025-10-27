<?php
class AuthController {
    private \PDO $pdo;

    public function __construct() {
        $this->pdo = Database::getConnection();
    }

    private function ensureUsersTable(): void {
        // Cria tabela apenas se não existir, usando esquema compatível
        $driver = Database::driver();
        if ($driver === 'pgsql') {
            $schema = Database::schema() ?: 'public';
            $existsSql = "SELECT 1 FROM information_schema.tables WHERE table_schema = :schema AND table_name = 'usuarios'";
            $chk = $this->pdo->prepare($existsSql);
            $chk->execute(['schema' => $schema]);
            if (!$chk->fetch()) {
                $sql = "CREATE TABLE usuarios (\n                    id BIGSERIAL PRIMARY KEY,\n                    nome TEXT NOT NULL,\n                    celular TEXT,\n                    email TEXT NOT NULL UNIQUE,\n                    login TEXT NOT NULL,\n                    senha_hash TEXT NOT NULL,\n                    datacadastro TIMESTAMP NOT NULL DEFAULT NOW(),\n                    ultimoacesso TIMESTAMP,\n                    ativo BOOLEAN NOT NULL DEFAULT TRUE\n                )";
                $this->pdo->exec($sql);
            }
        } else {
            // MySQL
            $existsSql = "SELECT 1 FROM information_schema.tables WHERE table_schema = DATABASE() AND table_name = 'usuarios'";
            $chk = $this->pdo->query($existsSql);
            if (!$chk->fetch()) {
                $sql = "CREATE TABLE usuarios (\n                    id INT AUTO_INCREMENT PRIMARY KEY,\n                    nome VARCHAR(255) NOT NULL,\n                    celular VARCHAR(50),\n                    email VARCHAR(255) NOT NULL UNIQUE,\n                    login VARCHAR(100) NOT NULL,\n                    senha_hash VARCHAR(255) NOT NULL,\n                    datacadastro TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,\n                    ultimoacesso TIMESTAMP NULL,\n                    ativo TINYINT(1) NOT NULL DEFAULT 1\n                ) ENGINE=InnoDB";
                $this->pdo->exec($sql);
            }
        }
    }

    private function ensureAdminUser(): void {
        // Descobre colunas para montar INSERT compatível
        $driver = Database::driver();
        $schema = Database::schema();
        if ($driver === 'pgsql') {
            $sql = "SELECT column_name, is_nullable, column_default\n                    FROM information_schema.columns\n                    WHERE table_name = 'usuarios' AND table_schema = :schema";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(['schema' => $schema ?: 'public']);
        } else {
            $sql = "SELECT COLUMN_NAME AS column_name, IS_NULLABLE AS is_nullable, COLUMN_DEFAULT AS column_default\n                    FROM information_schema.columns\n                    WHERE TABLE_NAME = 'usuarios' AND TABLE_SCHEMA = DATABASE()";
            $stmt = $this->pdo->query($sql);
        }
        $cols = $stmt->fetchAll();
        $colNames = array_map(fn($r) => $r['column_name'], $cols);
        $hasLogin = in_array('login', $colNames, true);
        $needsAtivo = false;
        foreach ($cols as $c) {
            if ($c['column_name'] === 'ativo' && strtoupper($c['is_nullable']) === 'NO' && ($c['column_default'] === null || $c['column_default'] === '')) {
                $needsAtivo = true;
                break;
            }
        }

        // Já existe admin?
        if ($hasLogin) {
            $check = $this->pdo->prepare("SELECT COUNT(*) AS c FROM usuarios WHERE login = ? OR email = ?");
            $check->execute(['admin', 'admin@localhost']);
        } else {
            $check = $this->pdo->prepare("SELECT COUNT(*) AS c FROM usuarios WHERE email = ?");
            $check->execute(['admin@localhost']);
        }
        $count = (int)($check->fetch()['c'] ?? 0);
        if ($count > 0) return;

        $hash = password_hash('admin123', PASSWORD_DEFAULT);
        if ($hasLogin && $needsAtivo) {
            $ins = $this->pdo->prepare("INSERT INTO usuarios (nome, email, login, senha_hash, ativo) VALUES (?,?,?,?,TRUE)");
            $ins->execute(['Admin', 'admin@localhost', 'admin', $hash]);
        } elseif ($hasLogin) {
            $ins = $this->pdo->prepare("INSERT INTO usuarios (nome, email, login, senha_hash) VALUES (?,?,?,?)");
            $ins->execute(['Admin', 'admin@localhost', 'admin', $hash]);
        } else {
            $ins = $this->pdo->prepare("INSERT INTO usuarios (nome, email, senha_hash) VALUES (?,?,?)");
            $ins->execute(['Admin', 'admin@localhost', $hash]);
        }
    }

    public function login(): void {
        $this->ensureUsersTable();
        $this->ensureAdminUser();
        $viewFile = __DIR__ . '/../views/auth/login.php';
        $error = null;
        require __DIR__ . '/../views/layout.php';
    }

    public function doLogin(): void {
        $this->ensureUsersTable();
        $email = trim($_POST['email'] ?? '');
        $senha = $_POST['senha'] ?? '';
        $error = null;
        if ($email === '' || $senha === '') {
            $error = 'Informe email e senha.';
            $viewFile = __DIR__ . '/../views/auth/login.php';
            require __DIR__ . '/../views/layout.php';
            return;
        }
        $stmt = $this->pdo->prepare("SELECT id, nome, email, login, senha_hash FROM usuarios WHERE email = ? LIMIT 1");
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        if (!$user || !password_verify($senha, $user['senha_hash'])) {
            $error = 'Credenciais inválidas.';
            $viewFile = __DIR__ . '/../views/auth/login.php';
            require __DIR__ . '/../views/layout.php';
            return;
        }
        $_SESSION['user'] = [
            'id' => $user['id'],
            'nome' => $user['nome'],
            'email' => $user['email'],
            'login' => $user['login'] ?? ''
        ];
        header('Location: ?');
        exit;
    }

    public function logout(): void {
        unset($_SESSION['user']);
        session_destroy();
        header('Location: ?controller=auth&action=login');
        exit;
    }
}