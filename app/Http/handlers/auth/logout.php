<?php
require_once __DIR__ . '/../../BaseHandler.php';
require_once __DIR__ . '/../../../auth/SessionManager.php';

class LogoutHandler extends BaseHandler {
    protected $requiredStatus = AccessLevels::USER;
    
    public function handle() {
        $sessionManager = new SessionManager();
        $sessionManager->logout();
        
        $this->redirect('/login');
    }
}

LogoutHandler::run();
