<?php
declare(strict_types=1);
namespace App\Exceptions;

use Exception;

class AuthorizationException extends Exception {
    protected $message = 'access_denied';
    protected $code = 403;
}
