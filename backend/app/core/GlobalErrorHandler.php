<?php

declare(strict_types=1);

namespace App\Core;

use Throwable;

class ErrorHandler
{

    /**
     * Handles uncaught exceptions and generates a JSON error response.
     *
     * @param Throwable $exception The uncaught exception to handle.
     *
     * @return void
     */
    public static function handle(Throwable $exception): void
    {
        // set default status code
        $statusCode = HTTP_INTERNAL_SERVER_ERROR;
        
        // set status code from exception if it's a valid HTTP status code
        if ($exception->getCode() !== 0 && is_int($exception->getCode()))
        {
            $statusCode = $exception->getCode();
        }
        
        // Ensure status code is within valid HTTP range
        if ($statusCode < 100 || $statusCode > 599)
        {
            $statusCode = 500;
        }

        // Log the error
        logMessage(ERROR , $exception->getMessage());

        $response = [
            'error' => true,
            'message' => $exception->getMessage(),
        ];

        // Set headers and status code
        if (!headers_sent())
        {
            http_response_code($statusCode);
            header('Content-Type: application/json; charset=utf-8');
        }

        echo json_encode($response, JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE);

        exit ;
    }
}
