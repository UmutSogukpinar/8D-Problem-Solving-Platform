<?php

declare(strict_types=1);

namespace App\Exceptions;

use Exception;

class NotFoundException extends Exception
{

    public function __construct($id = null, string $resource = "Resource")
    {
        $message = $id 
            ? "The requested {$resource} with ID {$id} could not be found." 
            : "The requested {$resource} could not be found.";

        parent::__construct($message, HTTP_NOT_FOUND);
    }
}