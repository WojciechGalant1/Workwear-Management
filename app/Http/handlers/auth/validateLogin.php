<?php
require_once __DIR__ . '/../../BaseHandler.php';
require_once __DIR__ . '/../../../entities/User.php';
require_once __DIR__ . '/../../../auth/SessionManager.php';

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
        // Rate-limit using session
        $_SESSION['login_attempts'] = ($_SESSION['login_attempts'] ?? 0) + 1;
        
        if ($_SESSION['login_attempts'] > 20) {
            usleep(500000); // 0.5s delay
        }
        
        $stmt = $pdo->prepare('SELECT * FROM uzytkownicy WHERE id_id = :kodID LIMIT 1');
        $stmt->execute([':kodID' => $kodID]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user) {
            $this->createSession($user);
            $this->successResponse('login_success');
        } else {
            $this->errorResponse('login_invalid_code');
        }
    }
    
    private function createSession(array $user): void {
        $sessionManager = new SessionManager();
        $sessionManager->login($user['id'], $user['status']);
    }
}

ValidateLoginHandler::run();
