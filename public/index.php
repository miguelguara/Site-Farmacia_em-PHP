<?php
spl_autoload_register(function($class) {
    $paths = [
        __DIR__ . '/../core/' . $class . '.php',
        __DIR__ . '/../controllers/' . $class . '.php',
        __DIR__ . '/../models/' . $class . '.php'
    ];
    foreach ($paths as $p) {
        if (file_exists($p)) { require_once $p; return; }
    }
});

// Roteamento simples via query params
$controllerName = isset($_GET['controller']) ? ucfirst(strtolower($_GET['controller'])) . 'Controller' : 'HomeController';
$action = $_GET['action'] ?? 'index';

if (!class_exists($controllerName)) {
    http_response_code(404);
    echo "Controller não encontrado";
    exit;
}

$controller = new $controllerName();

if (!method_exists($controller, $action)) {
    http_response_code(404);
    echo "Ação não encontrada";
    exit;
}

$controller->$action();