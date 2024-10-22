<?php

namespace Vertuoza\Exceptions;

use Throwable;

class BadRequestException extends BusinessException
{
    public const code = 400;

    public function __construct(string $message, string $errorCode = "BAD_REQUEST", Throwable $previous = null, array|null $args = null)
    {
        parent::__construct($message, $errorCode, BadRequestException::code, $previous, $args);
    }
}
