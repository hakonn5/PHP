<?php

// Front Controller / Entry Point
require_once __DIR__ . '/../config/helpers.php';

// Parse URL
$requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
// Remove trailing slash if not root
if ($requestUri !== '/' && substr($requestUri, -1) === '/') {
    $requestUri = rtrim($requestUri, '/');
}

// Simple Router
switch ($requestUri) {
    case '/':
        require_once __DIR__ . '/../controllers/HomeController.php';
        (new HomeController())->index();
        break;

    case '/login':
        require_once __DIR__ . '/../controllers/AuthController.php';
        (new AuthController())->login();
        break;

    case '/register':
        require_once __DIR__ . '/../controllers/AuthController.php';
        (new AuthController())->register();
        break;

    case '/logout':
        require_once __DIR__ . '/../controllers/AuthController.php';
        (new AuthController())->logout();
        break;

    case '/forgot-password':
        require_once __DIR__ . '/../controllers/AuthController.php';
        (new AuthController())->forgotPassword();
        break;

    case '/resources/search':
        require_once __DIR__ . '/../controllers/ResourceController.php';
        (new ResourceController())->search();
        break;

    case '/resources/create':
        require_once __DIR__ . '/../controllers/ResourceController.php';
        (new ResourceController())->create();
        break;

    case '/admin/users':
        require_once __DIR__ . '/../controllers/AdminController.php';
        (new AdminController())->users();
        break;

    case '/admin/resources':
        require_once __DIR__ . '/../controllers/AdminController.php';
        (new AdminController())->resources();
        break;

    case '/admin/categories':
        require_once __DIR__ . '/../controllers/AdminController.php';
        (new AdminController())->categories();
        break;

    default:
        http_response_code(404);
        echo "404 Not Found";
        break;
}
