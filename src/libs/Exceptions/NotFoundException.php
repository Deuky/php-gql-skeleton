<?php

namespace Vertuoza\Libs\Exceptions;

use Throwable;

class NotFoundException extends BusinessException
{
    public const code = 404;

    public function __construct(string $message = "The resource requested is not available", string $errorCode = "NOT_FOUND", Throwable $previous = null, array|null $args = null)
    {
        parent::__construct($message, $errorCode, NotFoundException::code, $previous, $args);
    }
}
