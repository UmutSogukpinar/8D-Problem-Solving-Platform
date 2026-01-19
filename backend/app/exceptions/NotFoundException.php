<?php

declare(strict_types=1);

namespace App\Exceptions;

use Exception;

class NotFoundException extends Exception
{
    public function __construct(string $message = "Resource not found")
    {
        parent::__construct($message, HTTP_NOT_FOUND);
    }
}
