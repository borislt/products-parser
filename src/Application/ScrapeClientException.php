<?php

declare(strict_types=1);

namespace App\Application;

use Exception;
use Throwable;

class ScrapeClientException extends Exception
{
    public function __construct(string $message, int $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
