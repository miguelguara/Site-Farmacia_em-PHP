<?php
session_start();
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

// Proteção de rotas: requer login para qualquer controller exceto AuthController, AboutController e ImgController
if (!isset($_SESSION['user']) && !in_array($controllerName, ['AuthController', 'AboutController', 'ImgController'])) {
    header('Location: ?controller=auth&action=login');
    exit;
}

// Controle de permissões simples por login
$loginStr = strtolower($_SESSION['user']['login'] ?? '');
$hasFullAccess = (str_contains($loginStr, 'admin') || str_contains($loginStr, 'farma'));
$hasDispOnly = (!$hasFullAccess && str_contains($loginStr, 'atendente'));

if (!$hasFullAccess) {
    if ($hasDispOnly) {
        if ($controllerName === 'TableController') {
            $name = strtolower($_GET['name'] ?? '');
            if ($name !== 'dispensacoes') {
                http_response_code(403);
                echo 'Acesso restrito: apenas Dispensações.';
                exit;
            }
    } else if (!in_array($controllerName, ['HomeController', 'AuthController', 'AboutController', 'ImgController'])) {
        http_response_code(403);
        echo 'Acesso restrito: apenas Dispensações.';
        exit;
    }
    } else {
        // Usuários sem perfil conhecido: permitir apenas autenticação
        if ($controllerName !== 'AuthController') {
            header('Location: ?controller=auth&action=login');
            exit;
        }
    }
}

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