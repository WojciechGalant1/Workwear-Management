<?php
declare(strict_types=1);

namespace Tests\Unit\Auth;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\Attributes\CoversClass;
use App\Auth\AccessGuard;
use App\Auth\SessionManager;
use App\Repositories\UserRepository;
use App\Exceptions\AuthException;
use App\Exceptions\AccessDeniedException;

#[CoversClass(AccessGuard::class)]
class AccessGuardTest extends TestCase
{
    private AccessGuard $guard;
    private MockObject $sessionManager;
    private MockObject $userRepo;

    protected function setUp(): void
    {
        $this->sessionManager = $this->createMock(SessionManager::class);
        $this->userRepo = $this->createMock(UserRepository::class);
        
        $this->guard = new AccessGuard($this->sessionManager, $this->userRepo);
    }

    public function testShouldReturnTrueForIsAuthenticatedWhenSessionSaysSo(): void
    {
        $this->sessionManager->method('isLoggedIn')->willReturn(true);
        $this->assertTrue($this->guard->isAuthenticated());
    }

    public function testShouldReturnUserIdFromSession(): void
    {
        $this->sessionManager->method('getUserId')->willReturn(123);
        $this->assertSame(123, $this->guard->getUserId());
    }

    public function testShouldReturnFalseForHasRequiredStatusWhenUserNotLoggedIn(): void
    {
        $this->sessionManager->method('getUserId')->willReturn(null);
        $this->assertFalse($this->guard->hasRequiredStatus(1));
    }

    public function testShouldReturnTrueForHasRequiredStatusWhenUserHasHigherStatus(): void
    {
        $userId = 1;
        $requiredStatus = 1;

        $this->sessionManager->method('getUserId')->willReturn($userId);
        $this->userRepo->method('getUserById')
            ->with($userId)
            ->willReturn(['id' => $userId, 'status' => 2]); // Status 2 > 1

        $this->assertTrue($this->guard->hasRequiredStatus($requiredStatus));
    }

    public function testShouldThrowAuthExceptionWhenRequiringStatusAndNotLoggedIn(): void
    {
        $this->sessionManager->method('isLoggedIn')->willReturn(false);
        
        $this->expectException(AuthException::class);
        $this->guard->requireStatus(1);
    }

    public function testShouldThrowAccessDeniedExceptionWhenStatusIsInsufficient(): void
    {
        $userId = 1;
        $requiredStatus = 2; // Admin

        $this->sessionManager->method('isLoggedIn')->willReturn(true);
        $this->sessionManager->method('getUserId')->willReturn($userId);
        
        // User has status 1 (User), but 2 is required
        $this->userRepo->method('getUserById')
            ->willReturn(['id' => $userId, 'status' => 1]);

        $this->expectException(AccessDeniedException::class);
        $this->guard->requireStatus($requiredStatus);
    }

    public function testShouldAllowAccessWhenStatusIsSufficient(): void
    {
        $userId = 1;
        $requiredStatus = 2;

        $this->sessionManager->method('isLoggedIn')->willReturn(true);
        $this->sessionManager->method('getUserId')->willReturn($userId);
        $this->userRepo->method('getUserById')
            ->willReturn(['id' => $userId, 'status' => 2]);

        $this->guard->requireStatus($requiredStatus);
        
        // If no exception is thrown, test passes
        $this->assertTrue(true);
    }
}
