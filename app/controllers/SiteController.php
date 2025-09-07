<?php
require_once __DIR__ . '/../models/SiteChecker.php';

class SiteController {
    private $checker;
    
    public function __construct() {
        $this->checker = new SiteChecker();
    }
    
    public function index() {
        global $defaultSites;
        
        // Получаем данные из кэша для отображения начального состояния
        $cachedResults = [];
        foreach ($defaultSites as $name => $url) {
            $cachedResult = $this->checker->getCachedResult($url);
            if ($cachedResult) {
                $cachedResults[$name] = $cachedResult;
            }
        }
        
        // Подключаем вид
        $this->renderView('index', [
            'defaultSites' => $defaultSites,
            'cachedResults' => $cachedResults
        ]);
    }
    
    public function checkSingle() {
        $url = isset($_POST['url']) ? trim($_POST['url']) : '';
        $force = isset($_POST['force']) ? (bool)$_POST['force'] : false;
        
        if (empty($url)) {
            echo json_encode(['error' => 'URL не указан']);
            exit;
        }
        
        // Добавляем http:// если нет схемы и проверяем URL
        if (!preg_match("~^(?:f|ht)tps?://~i", $url)) {
            $url = "https://" . $url;
        }
        
        // Проверяем, что это валидный URL
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            echo json_encode(['error' => 'Неверный URL']);
            exit;
        }
        
        // Проверяем сайт (с принудительной проверкой если нужно)
        $result = $this->checker->checkSite($url, $force);
        
        echo json_encode($result);
    }
    
    private function renderView($viewName, $data = []) {
        extract($data);
        require_once __DIR__ . '/../views/layout.php';
    }
}
?>