<?php
/**
 * Base class for all HTTP handlers (AJAX/form POST)
 */
abstract class BaseHandler {
    protected $serviceContainer;
    protected $requireSession = true;
    protected $requireLocalization = true;
    protected $requiredStatus = null;
    
    public function __construct() {
        $this->loadDependencies();
        
        if ($this->requireSession) {
            $this->initSession();
        }
        
        if ($this->requireLocalization) {
            $this->initLocalization();
        }
        
        if ($this->requiredStatus !== null) {
            $this->checkAccessStatus();
        }
        
        $this->serviceContainer = ServiceContainer::getInstance();
    }
    
    private function initSession() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }
    
    private function loadDependencies() {
        require_once __DIR__ . '/../core/ServiceContainer.php';
        require_once __DIR__ . '/../auth/CsrfGuard.php';
        require_once __DIR__ . '/../auth/AccessGuard.php';
        require_once __DIR__ . '/../config/AccessLevels.php';
        require_once __DIR__ . '/../helpers/LocalizationHelper.php';
        require_once __DIR__ . '/../helpers/LanguageSwitcher.php';
        require_once __DIR__ . '/../helpers/UrlHelper.php';
    }
    
    private function initLocalization() {
        LanguageSwitcher::initializeWithRouting();
    }
    
    private function checkAccessStatus() {
        $guard = new AccessGuard();
        
        if (!$guard->isAuthenticated()) {
            http_response_code(401);
            $this->jsonResponse(array(
                'success' => false,
                'message' => LocalizationHelper::translate('error_session'),
                'redirect' => '/login'
            ));
        }
        
        if (!$guard->hasRequiredStatus($this->requiredStatus)) {
            http_response_code(403);
            $this->jsonResponse(array(
                'success' => false,
                'message' => LocalizationHelper::translate('access_denied')
            ));
        }
    }
    
    /**
     * Walidacja tokenu CSRF
     * @param array|null $data Dane JSON (jeśli null, używa $_POST)
     * @return bool
     */
    protected function validateCsrf($data = null) {
        if ($data !== null) {
            return CsrfGuard::validateTokenFromJson($data);
        }
        return CsrfGuard::validateToken();
    }
    
    /**
     * Wysyła odpowiedź JSON i kończy skrypt
     * @param array $data Dane do wysłania
     */
    protected function jsonResponse($data) {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data);
        exit;
    }
    
    /**
     * Wysyła odpowiedź błędu
     * @param string $message Komunikat błędu (klucz tłumaczenia lub tekst)
     * @param bool $translate Czy tłumaczyć komunikat
     */
    protected function errorResponse($message, $translate = true) {
        $msg = $translate ? LocalizationHelper::translate($message) : $message;
        $this->jsonResponse(array('success' => false, 'message' => $msg));
    }
    
    /**
     * Wysyła odpowiedź sukcesu
     * @param string|null $message Komunikat (opcjonalny)
     * @param array $data Dodatkowe dane
     * @param bool $translate Czy tłumaczyć komunikat
     */
    protected function successResponse($message = null, $data = array(), $translate = true) {
        $response = array('success' => true);
        
        if ($message !== null) {
            $response['message'] = $translate ? LocalizationHelper::translate($message) : $message;
        }
        
        $this->jsonResponse(array_merge($response, $data));
    }
    
    /**
     * Wysyła odpowiedź błędu CSRF
     */
    protected function csrfErrorResponse() {
        http_response_code(403);
        $this->jsonResponse(CsrfGuard::getErrorResponse());
    }
    
    /**
     * Pobiera repozytorium z ServiceContainer
     * @param string $name Nazwa repozytorium
     * @return mixed
     */
    protected function getRepository($name) {
        return $this->serviceContainer->getRepository($name);
    }
    
    /**
     * Pobiera serwis z ServiceContainer
     * @param string $name Nazwa serwisu
     * @return mixed
     */
    protected function getService($name) {
        return $this->serviceContainer->getService($name);
    }
    
    /**
     * Pobiera dane z JSON body requestu
     * @return array|null
     */
    protected function getJsonInput() {
        return json_decode(file_get_contents('php://input'), true);
    }
    
    /**
     * Pobiera ID zalogowanego użytkownika
     * @return int|null
     */
    protected function getUserId() {
        return isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
    }
    
    /**
     * Sprawdza czy request jest POST
     * @return bool
     */
    protected function isPost() {
        return $_SERVER['REQUEST_METHOD'] === 'POST';
    }
    
    /**
     * Tłumaczy klucz
     * @param string $key Klucz tłumaczenia
     * @return string
     */
    protected function translate($key) {
        return LocalizationHelper::translate($key);
    }
    
    /**
     * Przekierowuje do podanej ścieżki
     * @param string $path Ścieżka względna (np. '/login')
     */
    protected function redirect($path) {
        $baseUrl = UrlHelper::getAppBaseUrl();
        header('Location: ' . $baseUrl . $path);
        exit;
    }
    
    /**
     * Główna metoda obsługi requestu - do implementacji w klasach pochodnych
     */
    abstract public function handle();
    
    /**
     * Statyczna metoda do szybkiego uruchomienia handlera
     */
    public static function run() {
        $handler = new static();
        $handler->handle();
    }
}
