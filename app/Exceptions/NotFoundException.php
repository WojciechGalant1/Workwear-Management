<?php
declare(strict_types=1);
namespace App\Exceptions;

use Exception;

class NotFoundException extends Exception {
    protected $message = 'error_not_found';
    protected $code = 404;
}
