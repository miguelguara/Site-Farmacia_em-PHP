<?php

class ImgController {
    public function get() {
        $name = $_GET['name'] ?? '';
        // Whitelist allowed images to avoid path traversal
        $allowed = [
            'HeartSyringe.jpg' => 'image/jpeg',
            'CodeMonkey.jpg'   => 'image/jpeg',
            'HeartSyringe.png' => 'image/png',
            'CodeMonkey.png'   => 'image/png',
        ];
        if (!isset($allowed[$name])) {
            http_response_code(404);
            echo 'Imagem não encontrada';
            return;
        }
        $path = __DIR__ . '/../Img/' . $name;
        if (!is_file($path)) {
            http_response_code(404);
            echo 'Arquivo não encontrado';
            return;
        }
        header('Content-Type: ' . $allowed[$name]);
        header('Content-Length: ' . filesize($path));
        readfile($path);
        exit;
    }
}