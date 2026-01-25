<?php
declare(strict_types=1);
namespace App\Http\Handlers\Auth;

require_once __DIR__ . '/../../../handler_bootstrap.php';

use App\Http\BaseHandler;
use App\Auth\SessionManager;
use \PDO;

class ValidateLoginHandler extends BaseHandler {
    
    public function handle(): void {
        if ($this->isPost() && !$this->validateCsrf()) {
            $this->csrfErrorResponse();
        }
        
        $username = trim($_POST['username'] ?? '');
        $password = trim($_POST['password'] ?? '');
        $kodID = trim($_POST['kodID'] ?? '');
        
        $pdo = $this->serviceContainer->getPdo();
        
        if (!empty($username) && !empty($password)) {
            $this->loginWithPassword($pdo, $username, $password);
        } elseif (!empty($kodID)) {
            $this->loginWithCode($pdo, $kodID);
        } else {
            $this->errorResponse('login_no_credentials');
        }
    }
    
    private function loginWithPassword(PDO $pdo, string $username, string $password): void {
        $stmt = $pdo->prepare('SELECT * FROM uzytkownicy WHERE nazwa = :username LIMIT 1');
        $stmt->execute([':username' => $username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user) {
            $hashedPassword = $user['password'];
            if (crypt($password, $hashedPassword) == $hashedPassword) {
                $this->createSession($user);
                $this->successResponse('login_success');
            } else {
                $this->errorResponse('login_invalid_credentials');
            }
        } else {
            $this->errorResponse('login_invalid_credentials');
        }
    }
    
    private function loginWithCode(PDO $pdo, string $kodID): void {
        // Rate-limit using RateLimiter helper with IP-based key
        $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        $key = 'login_code_' . $ip;
        
        // Allow 20 attempts per minute
        if (!\App\Helpers\RateLimiter::check($key, 20, 60)) {
            // Block request immediately
            http_response_code(429); // Too Many Requests
            $this->errorResponse('login_too_many_attempts');
            return; 
        }
        
        $stmt = $pdo->prepare('SELECT * FROM uzytkownicy WHERE id_id = :kodID LIMIT 1');
        $stmt->execute([':kodID' => $kodID]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user) {
            \App\Helpers\RateLimiter::clear($key); // Clear on success
            $this->createSession($user);
            $this->successResponse('login_success');
        } else {
            $this->errorResponse('login_invalid_code');
        }
    }
    
    private function createSession(array $user): void {
        $sessionManager = new SessionManager();
        $sessionManager->login((int)$user['id'], (int)$user['status']);
    }
}

ValidateLoginHandler::run();
