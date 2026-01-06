<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Mukhoiran\MVCProject\App\Router;
use Mukhoiran\MVCProject\Controller\HomeController;
use Mukhoiran\MVCProject\Controller\ProductController;
use Mukhoiran\MVCProject\Middleware\AuthMiddleware;

Router::add('GET', '/products/([0-9a-zA-Z]*)/categories/([0-9a-zA-Z]*)', ProductController::class, 'categories');
Router::add('GET', '/', HomeController::class, 'index');
Router::add('GET', '/hello', HomeController::class, 'hello', [AuthMiddleware::class]);
Router::add('GET', '/world', HomeController::class, 'world', [AuthMiddleware::class]);

Router::run();