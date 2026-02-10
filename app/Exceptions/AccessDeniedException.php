<?php
declare(strict_types=1);
namespace App\Exceptions;

use Exception;

class AccessDeniedException extends Exception {
    protected $message = 'access_denied';
    protected $code = 403;
}
