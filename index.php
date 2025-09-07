<?php
require_once 'config.php';

// Если это AJAX запрос для проверки сайта
if (isset($_GET['action']) && $_GET['action'] == 'check') {
    require_once 'app/controllers/SiteController.php';
    $controller = new SiteController();
    $controller->checkSingle();
    exit;
}

// Простой роутер для обычных запросов
$url = isset($_GET['url']) ? $_GET['url'] : 'site/index';
$urlParts = explode('/', $url);

$controllerName = ucfirst($urlParts[0]) . 'Controller';
$actionName = isset($urlParts[1]) ? $urlParts[1] : 'index';

// Подключаем контроллер
$controllerFile = 'app/controllers/' . $controllerName . '.php';
if (file_exists($controllerFile)) {
    require_once $controllerFile;
    $controller = new $controllerName();
    
    if (method_exists($controller, $actionName)) {
        $controller->$actionName();
    } else {
        http_response_code(404);
        echo "Страница не найдена";
    }
} else {
    http_response_code(404);
    echo "Страница не найдена";
}
?>