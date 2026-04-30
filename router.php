<?php

$url = isset($_GET['url']) ? $_GET['url'] : '';
$url = trim($url, '/');
$url = filter_var($url, FILTER_SANITIZE_URL);
$urlParts = explode('/', $url);

$controllerName = 'LandingController';
$methodName = 'index';

if (!empty($urlParts[0])) {
    $part0 = strtolower($urlParts[0]);
    
    // Map URL names to Controller names
    $routes = [
        'auth' => 'AuthController',
        'santri' => 'SantriController',
        'admin' => 'AdminController',
        'sekretaris' => 'SekretarisController',
        'bendahara-reg' => 'BendaharaRegController',
        'bendahara-du' => 'BendaharaDUController',
        'mufatis' => 'MufatisController',
        'notification' => 'NotificationController'
    ];
    
    if (array_key_exists($part0, $routes)) {
        $controllerName = $routes[$part0];
        if (isset($urlParts[1])) {
            $methodName = str_replace('-', '_', $urlParts[1]);
        }
        unset($urlParts[0], $urlParts[1]);
        $urlParts = array_values($urlParts);
    } else {
        // If not in routes, default to LandingController methods
        $methodName = str_replace('-', '_', $urlParts[0]);
        unset($urlParts[0]);
        $urlParts = array_values($urlParts);
    }
}

$controllerFile = __DIR__ . '/controllers/' . $controllerName . '.php';

if (file_exists($controllerFile)) {
    require_once $controllerFile;
    if (class_exists($controllerName)) {
        $controller = new $controllerName();
        if (method_exists($controller, $methodName)) {
            call_user_func_array([$controller, $methodName], $urlParts);
        } else {
            http_response_code(404);
            require __DIR__ . '/views/errors/404.php';
        }
    } else {
        http_response_code(404);
        require __DIR__ . '/views/errors/404.php';
    }
} else {
    http_response_code(404);
    require __DIR__ . '/views/errors/404.php';
}
