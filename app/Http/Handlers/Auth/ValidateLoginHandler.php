<?php
declare(strict_types=1);
namespace App\Http\Handlers\Auth;

require_once __DIR__ . '/../../../handler_bootstrap.php';

use App\Http\BaseHandler;
use App\Auth\SessionManager;
use \PDO;

use App\Exceptions\ValidationException;
use App\Exceptions\AuthorizationException;
use App\Exceptions\AuthenticationException;
use App\Exceptions\RateLimitExceededException;

class ValidateLoginHandler extends BaseHandler {
    
    // Public access allowed (no required status)

    public function handle(): void {
        if ($this->isPost() && !$this->validateCsrf()) {
            throw new AuthorizationException('error_csrf');
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
            throw new ValidationException('login_no_credentials');
        }
    }
    
    private function loginWithPassword(PDO $pdo, string $username, string $password): void {
        // Protect password login - 5 attempts per minute
        $this->throttle('auth:login_pass', 5, 60);

        $stmt = $pdo->prepare('SELECT * FROM uzytkownicy WHERE nazwa = :username LIMIT 1');
        $stmt->execute([':username' => $username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user) {
            $hashedPassword = $user['password'];
            if (crypt($password, $hashedPassword) == $hashedPassword) {
                $this->createSession($user);
                $this->successResponse('login_success');
            } else {
                throw new AuthenticationException('login_invalid_credentials');
            }
        } else {
            // Consistent error message to prevent enumeration (same as invalid password)
            throw new AuthenticationException('login_invalid_credentials');
        }
    }
    
    private function loginWithCode(PDO $pdo, string $kodID): void {
        // Protect code login - 20 attempts per minute (as was original logic)
        $this->throttle('auth:login_code', 20, 60);
        
        $stmt = $pdo->prepare('SELECT * FROM uzytkownicy WHERE id_id = :kodID LIMIT 1');
        $stmt->execute([':kodID' => $kodID]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user) {
            $this->createSession($user);
            $this->successResponse('login_success');
        } else {
            throw new AuthenticationException('login_invalid_code');
        }
    }
    
    private function createSession(array $user): void {
        $sessionManager = new SessionManager();
        $sessionManager->login((int)$user['id'], (int)$user['status']);
    }
}

ValidateLoginHandler::run();
