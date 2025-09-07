<?php
class SiteChecker {
    private $cacheDir;
    
    public function __construct() {
        $this->cacheDir = CACHE_DIR;
    }
    
    public function getCachedResult($url) {
        $cacheKey = md5($url);
        $cacheFile = $this->cacheDir . $cacheKey . '.cache';
        
        if (file_exists($cacheFile) && (time() - filemtime($cacheFile)) < CACHE_TIME) {
            $result = unserialize(file_get_contents($cacheFile));
            $result['from_cache'] = true;
            return $result;
        }
        
        return null;
    }
    
    public function checkSite($url, $force = false) {
        // Если не принудительная проверка, пытаемся получить из кэша
        if (!$force) {
            $cachedResult = $this->getCachedResult($url);
            if ($cachedResult) {
                return $cachedResult;
            }
        }
        
        // Если кэш устарел или принудительная проверка, проверяем сайт
        $result = $this->checkSiteAvailability($url);
        $result['from_cache'] = false;
        
        // Сохраняем в кэш
        $cacheKey = md5($url);
        $cacheFile = $this->cacheDir . $cacheKey . '.cache';
        file_put_contents($cacheFile, serialize($result));
        
        return $result;
    }
    
    private function checkSiteAvailability($url) {
        $startTime = microtime(true);
        
        // Используем cURL для проверки доступности
        $ch = curl_init();
        
        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 10,
            CURLOPT_CONNECTTIMEOUT => 5,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_USERAGENT => 'Mozilla/5.0 (Downdetector Checker)',
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_NOBODY => true // Только заголовки
        ]);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $totalTime = microtime(true) - $startTime;
        
        $error = null;
        if (curl_error($ch)) {
            $error = curl_error($ch);
        }
        
        curl_close($ch);
        
        $isAvailable = ($httpCode >= 200 && $httpCode < 400) && !$error;
        
        return [
            'available' => $isAvailable,
            'http_code' => $httpCode,
            'response_time' => round($totalTime * 1000, 2), // в миллисекундах
            'checked_at' => date('Y-m-d H:i:s'),
            'url' => $url,
            'error' => $error
        ];
    }
}
?>