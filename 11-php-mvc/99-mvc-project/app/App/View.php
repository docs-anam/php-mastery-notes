<?php

namespace Mukhoiran\MVCProject\App;

class View
{
    public static function render(string $view, array $model = []): void
      {
        $viewPath = __DIR__ . "/../View/{$view}.php";
        if (file_exists($viewPath)) {
            extract($model);
            include $viewPath;
        } else {
            http_response_code(404);
            include __DIR__ . '/../View/404.php';
        }
    }
}
