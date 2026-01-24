<?php
require_once __DIR__ . '/../../BaseHandler.php';
require_once __DIR__ . '/../../../auth/SessionManager.php';

class LogoutHandler extends BaseHandler {
    protected ?int $requiredStatus = AccessLevels::USER;
    
    public function handle(): void {
        $sessionManager = new SessionManager();
        $sessionManager->logout();
        
        $this->redirect('/login');
    }
}

LogoutHandler::run();
