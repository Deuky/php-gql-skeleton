<?php

namespace Vertuoza\Exceptions;

require_once __DIR__ . "/ProvidesExceptionArgs.php";

use Exception;
use Throwable;

class BusinessException extends Exception implements ProvidesExceptionArgs
{
    protected string $errorCode;
    protected array|null $args;

    public function __construct(string $message, string $errorCode, $code = 500, Throwable $previous = null, array|null $args = null)
    {
        $this->args = $args;
        $this->errorCode = $errorCode;
        // make sure everything is assigned properly
        parent::__construct($message, $code, $previous);
    }

    // custom string representation of object
    public function __toString()
    {
        return __CLASS__ . ": [{$this->errorCode}]: {$this->message}\n";
    }

    public function getErrorCode(): string
    {
        return $this->errorCode;
    }

    public function getArgs(): array|null
    {
        return $this->args;
    }
}
