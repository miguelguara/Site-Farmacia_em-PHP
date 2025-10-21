<?php
require_once __DIR__ . '/../core/Database.php';

class TableModel {
    private \PDO $pdo;
    private string $driver;
    private ?string $schema;
    private string $table;
    private ?string $pk;

    public function __construct(string $table) {
        $this->pdo = Database::getConnection();
        $this->driver = Database::driver();
        $this->schema = Database::schema();
        $this->table = $table;
        $this->pk = $this->detectPrimaryKey();
    }

    public function getPrimaryKey(): ?string { return $this->pk; }

    private function detectPrimaryKey(): ?string {
        if ($this->driver === 'pgsql') {
            $sql = "SELECT kcu.column_name
                    FROM information_schema.table_constraints tc
                    JOIN information_schema.key_column_usage kcu
                      ON tc.constraint_name = kcu.constraint_name
                     AND tc.table_schema = kcu.table_schema
                    WHERE tc.constraint_type = 'PRIMARY KEY'
                      AND tc.table_schema = :schema AND tc.table_name = :table
                    LIMIT 1";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(['schema' => $this->schema, 'table' => $this->table]);
        } else {
            $sql = "SELECT k.COLUMN_NAME
                    FROM information_schema.table_constraints t
                    JOIN information_schema.key_column_usage k
                      ON t.CONSTRAINT_NAME = k.CONSTRAINT_NAME
                     AND t.TABLE_SCHEMA = k.TABLE_SCHEMA
                    WHERE t.CONSTRAINT_TYPE = 'PRIMARY KEY'
                      AND t.TABLE_SCHEMA = DATABASE() AND t.TABLE_NAME = :table
                    LIMIT 1";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(['table' => $this->table]);
        }
        $row = $stmt->fetch();
        return $row ? ($row['column_name'] ?? $row['COLUMN_NAME']) : null;
    }

    public function columns(): array {
        if ($this->driver === 'pgsql') {
            $sql = "SELECT column_name, data_type, is_nullable, column_default
                    FROM information_schema.columns
                    WHERE table_schema = :schema AND table_name = :table
                    ORDER BY ordinal_position";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(['schema' => $this->schema, 'table' => $this->table]);
        } else {
            $sql = "SELECT COLUMN_NAME as column_name, DATA_TYPE as data_type,
                           IS_NULLABLE as is_nullable, COLUMN_DEFAULT as column_default, EXTRA
                    FROM information_schema.columns
                    WHERE table_schema = DATABASE() AND table_name = :table
                    ORDER BY ORDINAL_POSITION";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(['table' => $this->table]);
        }
        return $stmt->fetchAll();
    }

    private function isAutoIncrement(array $col): bool {
        if ($this->driver === 'pgsql') {
            return !empty($col['column_default']) && str_contains($col['column_default'], 'nextval(');
        }
        $extra = strtolower($col['EXTRA'] ?? '');
        return str_contains($extra, 'auto_increment');
    }

    private function quotedTable(): string {
        if ($this->driver === 'pgsql') {
            $schema = $this->schema ? '"' . $this->schema . '".' : '';
            return $schema . '"' . $this->table . '"';
        }
        return '`' . $this->table . '`';
    }

    private function quoteIdent(string $col): string {
        return $this->driver === 'pgsql' ? '"' . $col . '"' : '`' . $col . '`';
    }

    public function all(int $limit = 50, int $offset = 0): array {
        $sql = "SELECT * FROM " . $this->quotedTable() . " LIMIT :limit OFFSET :offset";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function count(): int {
        $sql = "SELECT COUNT(*) AS c FROM " . $this->quotedTable();
        $stmt = $this->pdo->query($sql);
        $row = $stmt->fetch();
        return (int) $row['c'];
    }

    public function find($id): ?array {
        if (!$this->pk) return null;
        $sql = "SELECT * FROM " . $this->quotedTable() . " WHERE " . $this->quoteIdent($this->pk) . " = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $id]);
        $res = $stmt->fetch();
        return $res ?: null;
    }

    public function create(array $data): bool {
        $cols = $this->columns();
        $insertCols = [];
        $params = [];
        foreach ($cols as $col) {
            $name = $col['column_name'];
            if ($name === $this->pk && $this->isAutoIncrement($col)) continue;
            if (array_key_exists($name, $data)) {
                $insertCols[] = $this->quoteIdent($name);
                $params[':' . $name] = $data[$name] === '' ? null : $data[$name];
            }
        }
        if (empty($insertCols)) return false;
        $placeholders = implode(', ', array_keys($params));
        $sql = "INSERT INTO " . $this->quotedTable() . " (" . implode(', ', $insertCols) . ") VALUES (" . $placeholders . ")";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return true;
    }

    public function update($id, array $data): bool {
        if (!$this->pk) return false;
        $cols = $this->columns();
        $sets = [];
        $params = [];
        foreach ($cols as $col) {
            $name = $col['column_name'];
            if ($name === $this->pk) continue;
            if (array_key_exists($name, $data)) {
                $sets[] = $this->quoteIdent($name) . ' = :' . $name;
                $params[':' . $name] = $data[$name] === '' ? null : $data[$name];
            }
        }
        if (empty($sets)) return false;
        $params[':id'] = $id;
        $sql = "UPDATE " . $this->quotedTable() . " SET " . implode(', ', $sets) . " WHERE " . $this->quoteIdent($this->pk) . " = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return true;
    }

    public function delete($id): bool {
        if (!$this->pk) return false;
        $sql = "DELETE FROM " . $this->quotedTable() . " WHERE " . $this->quoteIdent($this->pk) . " = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $id]);
        return true;
    }
}