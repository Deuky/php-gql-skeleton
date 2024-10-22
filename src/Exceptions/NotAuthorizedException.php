<?php

namespace Vertuoza\Exceptions;

use Throwable;

class NotAuthorizedException extends BusinessException
{
    public const code = 401;

    public function __construct(Throwable $previous = null, array|null $args = null)
    {
        parent::__construct("You are not authenticated", 'UNAUTHORIZED', NotAuthorizedException::code, $previous, $args);
    }
}
