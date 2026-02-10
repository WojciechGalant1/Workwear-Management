<?php
declare(strict_types=1);
namespace App\Auth;

use App\Auth\SessionManager;
use App\Repositories\UserRepository;
use App\Exceptions\AuthException;
use App\Exceptions\AccessDeniedException;

class AccessGuard {
    private SessionManager $sessionManager;
    private UserRepository $userRepo;
    
    public function __construct(SessionManager $sessionManager, UserRepository $userRepo) {
        $this->sessionManager = $sessionManager;
        $this->userRepo = $userRepo;
    }
    
    public function isAuthenticated(): bool {
        return $this->sessionManager->isLoggedIn();
    }
    
    public function getUserId(): ?int {
        return $this->sessionManager->getUserId();
    }
    
    public function hasRequiredStatus(int $requiredStatus): bool {
        $userId = $this->sessionManager->getUserId();
        if (!$userId) {
            return false;
        }
        
        $user = $this->userRepo->getUserById($userId);
        return $user && (int)$user['status'] >= $requiredStatus;
    }
    
    /**
     * @throws AuthException
     * @throws AccessDeniedException
     */
    public function requireStatus(int $requiredStatus): void {
        if (!$this->isAuthenticated()) {
            throw new AuthException('User not authenticated');
        }
        
        if (!$this->hasRequiredStatus($requiredStatus)) {
            throw new AccessDeniedException('Insufficient permissions');
        }
    }
}
