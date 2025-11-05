<?php
// Executa seeds sem inserir novos usuários
// Lê e executa SQL de Config/seeds_no_users.sql

require __DIR__ . '/../core/Database.php';

function runSeeds(string $path): void {
    $pdo = Database::getConnection();
    $schema = Database::schema() ?? 'public';

    $sql = file_get_contents($path);
    if ($sql === false) {
        throw new RuntimeException("Não foi possível ler o arquivo: $path");
    }

    // Normaliza quebras de linha e remove comentários de linha
    $lines = preg_split('/\r?\n/', $sql);
    $filtered = [];
    foreach ($lines as $line) {
        // Remove comentários '--' e espaços à direita
        $line = preg_replace('/--.*$/', '', $line);
        $line = rtrim($line);
        // Ignora linhas vazias
        if ($line === '') continue;
        // Ignora BEGIN/COMMIT do arquivo para evitar conflito com transação do PDO
        if (preg_match('/^BEGIN;?$/i', $line)) continue;
        if (preg_match('/^COMMIT;?$/i', $line)) continue;
        // Mantém demais linhas
        $filtered[] = $line;
    }

    $normalized = implode("\n", $filtered);
    // Divide por ponto e vírgula preservando ;
    $parts = preg_split('/;\s*\n?/', $normalized, -1, PREG_SPLIT_NO_EMPTY);

    // Configura schema de trabalho
    $pdo->exec("SET search_path TO " . $pdo->quote($schema));

    $pdo->beginTransaction();
    try {
        foreach ($parts as $stmt) {
            $trimmed = trim($stmt);
            if ($trimmed === '') continue;
            // Reaplica ;
            $sqlStmt = $trimmed . ';';
            // Log curto da instrução
            $preview = substr(preg_replace('/\s+/', ' ', $trimmed), 0, 120);
            echo "Executando: $preview...\n";
            $pdo->exec($sqlStmt);
        }
        $pdo->commit();
        echo "Seeds executados com sucesso.\n";
    } catch (Throwable $e) {
        $pdo->rollBack();
        fwrite(STDERR, "Erro ao executar seeds: " . $e->getMessage() . "\n");
        fwrite(STDERR, "Falhou em: " . (isset($preview) ? $preview : '(desconhecido)') . "\n");
        exit(1);
    }
}

runSeeds(__DIR__ . '/../Config/seeds_no_users.sql');