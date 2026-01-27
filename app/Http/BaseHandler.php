<?php
declare(strict_types=1);
namespace App\Http;

use App\Core\ServiceContainer;
use App\Auth\AccessGuard;
use App\Auth\CsrfGuard;
use App\Config\AccessLevels;
use App\Helpers\LocalizationHelper;
use App\Helpers\LanguageSwitcher;
use App\Helpers\UrlHelper;
use App\Exceptions\AuthenticationException;
use App\Exceptions\AuthorizationException;
use App\Exceptions\RateLimitExceededException;
use App\Exceptions\ValidationException;
use App\Exceptions\NotFoundException;

/**
 * Base class for all HTTP handlers (AJAX/form POST)
 */
abstract class BaseHandler {
    protected ServiceContainer $serviceContainer;
    protected bool $requireSession = true;
    protected bool $requireLocalization = true;
    protected ?int $requiredStatus = null;
   
    public function __construct() {
        if ($this->requireSession) {
            $this->initSession();
        }
        
        if ($this->requireLocalization) {
            $this->initLocalization();
        }
        
        if ($this->requiredStatus !== null) {
            $this->checkAccessStatus();
        }
        
        // Enforce CSRF for state-changing requests
        $this->checkCsrf();
        
        $this->serviceContainer = ServiceContainer::getInstance();
    }
    
    private function checkCsrf(): void {
        if (CsrfGuard::requiresValidation()) {
            if (!$this->validateCsrf()) {
                $this->csrfErrorResponse();
                exit; 
            }
        }
    }
    
    private function initSession(): void {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }
    
    private function initLocalization(): void {
        LanguageSwitcher::initializeWithRouting();
    }
    
    private function checkAccessStatus(): void {
        $guard = new AccessGuard();
        
        if (!$guard->isAuthenticated()) {
            throw new AuthenticationException();
        }
        
        if (!$guard->hasRequiredStatus($this->requiredStatus)) {
            throw new AuthorizationException();
        }
    }
    

    /**
     * Ogranicza liczbę żądań dla danej akcji
     * @param string $actionKey Unikalny klucz akcji (np. 'search:products')
     * @param int $maxAttempts Maksymalna liczba prób
     * @param int $decaySeconds Czas wygasania w sekundach
     * @throws \App\Exceptions\RateLimitExceededException
     */
    protected function throttle(string $actionKey, int $maxAttempts = 60, int $decaySeconds = 60): void {
        $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        $userId = $this->getUserId();
        
        // Hybrid Key: rl:{action}:ip:{ip}(:user:{id})
        $key = "rl:{$actionKey}:ip:{$ip}";
        if ($userId) {
            $key .= ":user:{$userId}";
        }
        
        if (!\App\Helpers\RateLimiter::check($key, $maxAttempts, $decaySeconds)) {
            // Log the security event
            error_log("Rate limit exceeded for key: {$key}");
            
            throw new RateLimitExceededException('error_rate_limit_exceeded');
        }
    }

    /**
     * Walidacja tokenu CSRF
     * @param array|null $data Dane JSON (jeśli null, używa $_POST)
     * @return bool
     */
    protected function validateCsrf(?array $data = null): bool {
        if ($data !== null) {
            return CsrfGuard::validateTokenFromJson($data);
        }
        return CsrfGuard::validateToken();
    }
    
    /**
     * Wysyła odpowiedź JSON i kończy skrypt
     * @param array $data Dane do wysłania
     */
    protected function jsonResponse(array $data): void {
        header('Content-Type: application/json; charset=utf-8');
        try {
            $json = json_encode($data, JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE);
            echo $json;
        } catch (\JsonException $e) {
            error_log('JSON encode error: ' . $e->getMessage());
            http_response_code(500);
            $errorResponse = json_encode(['success' => false, 'message' => 'Internal server error'], JSON_UNESCAPED_UNICODE);
            // Fallback na hardcoded string jeśli nawet prosty json_encode się nie powiedzie (bardzo rzadkie)
            echo $errorResponse !== false ? $errorResponse : '{"success":false,"message":"Internal server error"}';
        }
        exit;
    }
    
    /**
     * Wysyła odpowiedź błędu
     * @param string $message Komunikat błędu (klucz tłumaczenia lub tekst)
     * @param bool $translate Czy tłumaczyć komunikat
     */
    protected function errorResponse(string $message, bool $translate = true): void {
        $msg = $translate ? LocalizationHelper::translate($message) : $message;
        $this->jsonResponse([
            'success' => false, 
            'message' => $msg,
            'error' => $msg // Compatibility with older JS expecting .error
        ]);
    }
    
    /**
     * Wysyła odpowiedź sukcesu
     * @param string|null $message Komunikat (opcjonalny)
     * @param array $data Dodatkowe dane
     * @param bool $translate Czy tłumaczyć komunikat
     */
    protected function successResponse(?string $message = null, array $data = [], bool $translate = true): void {
        $response = ['success' => true];
        
        if ($message !== null) {
            $response['message'] = $translate ? LocalizationHelper::translate($message) : $message;
        }
        
        $this->jsonResponse(array_merge($response, $data));
    }
    
    /**
     * Wysyła odpowiedź błędu CSRF
     */
    protected function csrfErrorResponse(): void {
        http_response_code(403);
        $this->jsonResponse(CsrfGuard::getErrorResponse());
    }
    
    /**
     * Pobiera repozytorium z ServiceContainer
     * @param string $name Nazwa repozytorium
     * @return object
     */
    protected function getRepository(string $name): object {
        return $this->serviceContainer->getRepository($name);
    }
    
    /**
     * Pobiera serwis z ServiceContainer
     * @param string $name Nazwa serwisu
     * @return object
     */
    protected function getService(string $name): object {
        return $this->serviceContainer->getService($name);
    }
    
    /**
     * Pobiera dane z JSON body requestu
     * @return array|null
     */
    protected function getJsonInput(): ?array {
        $input = file_get_contents('php://input');
        if ($input === false || $input === '') {
            return null;
        }
        try {
            $decoded = json_decode($input, true, 512, JSON_THROW_ON_ERROR);
            return is_array($decoded) ? $decoded : null;
        } catch (\JsonException $e) {
            error_log('JSON decode error: ' . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Pobiera ID zalogowanego użytkownika
     * @return int|null
     */
    protected function getUserId(): ?int {
        return $_SESSION['user_id'] ?? null;
    }
    
    /**
     * Sprawdza czy request jest POST
     * @return bool
     */
    protected function isPost(): bool {
        return ($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST';
    }
    
    /**
     * Tłumaczy klucz
     * @param string $key Klucz tłumaczenia
     * @return string
     */
    protected function translate(string $key): string {
        return LocalizationHelper::translate($key);
    }
    
    /**
     * Przekierowuje do podanej ścieżki
     * @param string $path Ścieżka względna (np. '/login')
     */
    protected function redirect(string $path): void {
        $baseUrl = UrlHelper::getAppBaseUrl();
        header('Location: ' . $baseUrl . $path);
        exit;
    }
    
    /**
     * Główna metoda obsługi requestu - do implementacji w klasach pochodnych
     */
    abstract public function handle(): void;
    
    /**
     * Statyczna metoda do szybkiego uruchomienia handlera
     * Obsługuje wyjątki RateLimitExceededException oraz inne wyjątki aplikacji
     */
    public static function run(): void {
        $handler = new static();
        try {
            $handler->handle();
        } catch (RateLimitExceededException $e) {
            http_response_code(429);
            $handler->errorResponse($e->getMessage());
        } catch (ValidationException $e) {
            http_response_code(400);
            $handler->errorResponse($e->getMessage());
        } catch (AuthenticationException $e) {
            http_response_code(401);
            $handler->jsonResponse([
                'success' => false,
                'message' => LocalizationHelper::translate($e->getMessage()),
                'redirect' => '/login'
            ]);
        } catch (AuthorizationException $e) {
            http_response_code(403);
            $handler->errorResponse($e->getMessage());
        } catch (NotFoundException $e) {
            http_response_code(404);
            $handler->errorResponse($e->getMessage());
        } catch (\Exception $e) {
            // Catch-all for unexpected errors
            error_log("Unhandled Exception: " . $e->getMessage());
            http_response_code(500);
            $handler->errorResponse('error_server');
        }
    }
}
