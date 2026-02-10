<?php
declare(strict_types=1);

namespace Tests\Unit\Helpers;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use App\Helpers\RateLimiter;

#[CoversClass(RateLimiter::class)]
class RateLimiterTest extends TestCase
{
    private string $testKey = 'test_limiter_key';

    protected function tearDown(): void
    {
        // Cleanup file created by RateLimiter
        RateLimiter::clear($this->testKey);
    }

    public function testShouldAllowRequestWithinLimit(): void
    {
        // Limit: 2 attempts
        $allowed1 = RateLimiter::check($this->testKey, 2, 60);
        $allowed2 = RateLimiter::check($this->testKey, 2, 60);

        $this->assertTrue($allowed1, 'First attempt should be allowed');
        $this->assertTrue($allowed2, 'Second attempt should be allowed');
    }

    public function testShouldBlockRequestExceedingLimit(): void
    {
        // Limit: 1 attempt
        RateLimiter::check($this->testKey, 1, 60);
        
        // Second attempt -> should fail
        $allowed = RateLimiter::check($this->testKey, 1, 60);

        $this->assertFalse($allowed, 'Second attempt should be blocked (limit 1)');
    }

    public function testShouldResetCounterAfterDecayTime(): void
    {
        // Limit: 1 attempt, decay: 1 second
        RateLimiter::check($this->testKey, 1, 1);
        
        // Blocked immediately
        $this->assertFalse(RateLimiter::check($this->testKey, 1, 1));

        // Wait for decay (1s + buffer)
        sleep(2);

        // Should be allowed again
        $allowed = RateLimiter::check($this->testKey, 1, 1);
        $this->assertTrue($allowed, 'Should be allowed after decay time passed');
    }

    public function testShouldClearCounterManually(): void
    {
        // Block it
        RateLimiter::check($this->testKey, 0, 60);
        $this->assertFalse(RateLimiter::check($this->testKey, 0, 60));

        // Clear
        RateLimiter::clear($this->testKey);

        // Should be fresh start (0 count so far, limit 1 allows 1)
        // Note: limit 1 allows 1st attempt (0 -> 1 <= 1)
        $this->assertTrue(RateLimiter::check($this->testKey, 1, 60));
    }

    public function testShouldHandleDifferentKeysIndependently(): void
    {
        $keyA = 'key_A';
        $keyB = 'key_B';

        // Block A
        RateLimiter::check($keyA, 0, 60);
        $this->assertFalse(RateLimiter::check($keyA, 0, 60));

        // B should be unaffected
        $this->assertTrue(RateLimiter::check($keyB, 1, 60));

        // Cleanup extra key
        RateLimiter::clear($keyA);
        RateLimiter::clear($keyB);
    }
}
