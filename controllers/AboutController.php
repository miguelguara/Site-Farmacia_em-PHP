<?php

class AboutController {
    public function index() {
        $this->render('about/index');
    }

    private function render(string $view, array $data = []) {
        extract($data);
        $viewFile = __DIR__ . '/../views/' . $view . '.php';
        $layout = __DIR__ . '/../views/layout.php';
        require $layout;
    }
}