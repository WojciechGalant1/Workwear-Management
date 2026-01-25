<?php
declare(strict_types=1);
namespace App\Helpers;

class RateLimiter {
    
    /**
     * Checks if the action is allowed (under the limit).
     * Automatically increments the counter.
     * Uses file-based storage with locking.
     * 
     * @param string $key Unique key for the action (e.g. 'login_ip_127.0.0.1')
     * @param int $maxAttempts Maximum number of attempts allowed
     * @param int $decaySeconds Time window execution in seconds
     * @return bool True if allowed, False if limit exceeded
     */
    public static function check(string $key, int $maxAttempts, int $decaySeconds): bool {
        self::garbageCollect($decaySeconds);

        $file = self::getStoragePath($key);
        $dir = dirname($file);

        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }

        $fp = fopen($file, 'c+');
        if (!$fp) {
            return true; // Fail open if can't write (avoid breaking app)
        }

        if (!flock($fp, LOCK_EX)) {
            fclose($fp);
            return true;
        }

        $content = stream_get_contents($fp);
        $data = $content ? json_decode($content, true) : null;
        $now = time();

        if (!$data || ($now - $data['start'] > $decaySeconds)) {
            $data = [
                'start' => $now,
                'attempts' => 0
            ];
        }

        $data['attempts']++;
        $allowed = $data['attempts'] <= $maxAttempts;

        ftruncate($fp, 0);
        rewind($fp);
        fwrite($fp, json_encode($data));
        fflush($fp);
        flock($fp, LOCK_UN);
        fclose($fp);

        return $allowed;
    }

    /**
     * Clears the rate limit counter for a given key.
     * 
     * @param string $key
     */
    public static function clear(string $key): void {
        $file = self::getStoragePath($key);
        if (file_exists($file)) {
            unlink($file);
        }
    }

    private static function getStoragePath(string $key): string {
        // Use hash for safe filename (handles IPv6 colons etc)
        $hash = md5($key);
        return __DIR__ . '/../../storage/cache/rate_limit/' . $hash . '.json';
    }

    /**
     * Randomly cleans up old rate limit files (Garbage Collection).
     * Probability: 2%
     */
    private static function garbageCollect(int $decaySeconds): void {
        if (rand(1, 50) !== 1) {
            return;
        }

        $dir = __DIR__ . '/../../storage/cache/rate_limit/';
        if (!is_dir($dir)) {
            return;
        }

        $files = glob($dir . '*.json');
        $now = time();

        foreach ($files as $file) {
            if (is_file($file)) {
                // Check file modification time as a proxy for freshness
                if ($now - filemtime($file) > $decaySeconds + 60) {
                    unlink($file);
                }
            }
        }
    }
}
// NOTE:
// Rate limiting is IP-based only.
// This is intentional for current project scope.
// Can be extended to IP+code or code-based limiter if needed.