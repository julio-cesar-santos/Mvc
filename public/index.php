<?php
date_default_timezone_set('America/Recife');
session_start();

define('BASE_URL', '/SiteQueijaria_MVC/public');
define('APP_PATH', __DIR__ . '/../app/');

// Autoloader para carregar Models e Controllers
spl_autoload_register(function ($class_name) {
    $paths = [APP_PATH . 'Controllers/', APP_PATH . 'Models/'];
    foreach ($paths as $path) {
        if (file_exists($path . $class_name . '.php')) {
            require_once $path . $class_name . '.php';
            return;
        }
    }
});

// Funções auxiliares de autenticação
function isAuthenticated() {
    return isset($_SESSION['user_id']);
}

function isAdmin() {
    return isset($_SESSION['is_admin']) && $_SESSION['is_admin'];
}

// Roteamento
$url = isset($_GET['url']) ? rtrim($_GET['url'], '/') : 'home';
$url = filter_var($url, FILTER_SANITIZE_URL);
$params = explode('/', $url);

$controllerName = !empty($params[0]) ? ucfirst(strtolower($params[0])) . 'Controller' : 'HomeController';
$methodName = isset($params[1]) ? strtolower($params[1]) : 'index';
unset($params[0], $params[1]);

if (class_exists($controllerName)) {
    $controller = new $controllerName();
    if (method_exists($controller, $methodName)) {
        call_user_func_array([$controller, $methodName], array_values($params));
    } else {
        http_response_code(404);
        echo "Erro 404: Método não encontrado.";
    }
} else {
    http_response_code(404);
    echo "Erro 404: Controller não encontrado.";
}