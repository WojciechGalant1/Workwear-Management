<?php
declare(strict_types=1);
namespace App\Exceptions;

use Exception;

class AuthenticationException extends Exception {
    protected $message = 'error_session';
    protected $code = 401;
}
