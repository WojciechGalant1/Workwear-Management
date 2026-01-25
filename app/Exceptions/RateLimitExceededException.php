<?php
declare(strict_types=1);
namespace App\Exceptions;

use Exception;

class RateLimitExceededException extends Exception {
    protected $message = 'Too many requests';
    protected $code = 429;
}
