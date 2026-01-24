<?php
namespace App\Http\Handlers\Auth;

require_once __DIR__ . '/../../../handler_bootstrap.php';

use App\Http\BaseHandler;
use App\Config\AccessLevels;
use App\Auth\SessionManager;

class LogoutHandler extends BaseHandler {
    protected ?int $requiredStatus = AccessLevels::USER;
    
    public function handle(): void {
        $sessionManager = new SessionManager();
        $sessionManager->logout();
        
        $this->redirect('/login');
    }
}

LogoutHandler::run();
